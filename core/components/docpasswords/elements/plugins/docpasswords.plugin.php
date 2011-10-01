<?php

/**
 * DocPassword Plugin
 *
 * Events: OnDocFormPrerender, OnDocFormRender, OnDocFormSave, OnDocFormDelete
 *  OnWebPageInit, OnWebPagePrerender, OnWebPageComplete
 *
 * @author     Arietis Software <code@arietis-software.com>
 * @copyright  Copyright (c) 2011 Arietis Software Innovations
 * @license    http://www.software.com/license/gnu/license.txt   GNU License Version 3
 *
 * @package docpassword
 *
 * @todo
 * use custom smarty cache path
 *
 */
$hasDevCorePath = $modx->getOption(
    'dev.core_path',
    null,
    false);

if (false !== $hasDevCorePath) {
    error_reporting(8191);
    ini_set('display_errors', true);
}

$corePath = $modx->getOption(
    'dev.core_path',
    null,
    $modx->getOption('core_path')) . 'components/docpasswords/';

$assetsUrl = $modx->getOption(
    'dev.assets_url',
    null,
    $modx->getOption('assets_url')) . 'components/docpasswords/';

$docPasswords = $modx->getService(
    'docpasswords',
    'Docpasswords',
    $corePath . 'model/docpasswords/',
    $scriptProperties);

if (!($docPasswords instanceof Docpasswords))
    return '';

//smarty init
$modx->getService('smarty', 'smarty.modSmarty');

//docpassword vars
$docPWObj = null;
$hasDocPassword = false;

// MODx events
switch ($modx->event->name) {

    case 'OnDocFormRender': //Manager event

        $modx->smarty->setTemplatePath($docPasswords->config['smartyPath']);

        $value = '';

        if (isset($scriptProperties['resource'])) { //user is editing a doc
            $docId = $scriptProperties['resource']->get('id');

            $docPWObj = $modx->getObject('Docpassword', array(
                'document_id' => (int) $docId
            ));

            if (!empty($docPWObj) && (boolean) $docId) {
                $modx->smarty->assign('value', $docPWObj->get('password'));
            }
        }

        $output = $modx->smarty->fetch('managerResourceFormField.smarty.tpl');

        $modx->event->output($output);

        //reset the smarty template path for MODx
        $modx->smarty->setTemplatePath(
            $modx->getOption('manager_path') . 'templates/default'
        );

        break;

    case 'OnDocFormSave': //Manager event

        $docId = $scriptProperties['resource']->get('id');
        $password = false;

        //get the password string
        if (array_key_exists('docpassword', $_POST)) {
            $p = trim($_POST['docpassword']);
            if (!empty($p)) {
                $password = $p;
            }
        }

        //get the doc obj by document_id
        $docPWObj = $modx->getObject('Docpassword', array(
            'document_id' => $docId
        ));

        if ((boolean) $docId) { //resource id exists
            if (empty($docPWObj)) { //no previous password:
                if (false !== $password) { //create it
                    $docPWObj = $modx->newObject('Docpassword', array(
                        'password' => $password,
                        'document_id' => $docId
                    ));
                    $docPWObj->save();
                }
            } else { //previous password:
                if (false !== $password) { //update it
                    $docPWObj->fromArray(array(
                        'password' => $password,
                        'document_id' => $docId
                    ));
                    $docPWObj->save();
                } else { //delete it
                    $docPWObj->remove();
                }
            }
            //finally make the doc resource uncacheable
            $modx->db->update(array('cacheable' => 0),
                              $modx->getFullTablename('site_content'),
                              'id=' . $docId);
        }

        break;

    case 'OnDocFormDelete': //Manager event

        $docId = $scriptProperties['resource']->get('id');

        if ((boolean) $docId) { //resource id exists
            $docPWObj = $modx->getObject('Docpassword', array(
                'document_id' => (int) $docId
            ));

            if (!empty($docPWObj)) {
                $docPWObj->remove();
            }
        }

        break;

    case 'OnWebPageInit': //Public event

        $modx->regClientCSS($docPasswords->config['cssUrl'] . 'docpasswords.css');
        $modx->regClientScript($docPasswords->config['jsUrl'] . 'docpasswords.js');

        break;

    case 'OnWebPagePrerender': //Public event

        $docId = $modx->resourceIdentifier;

        $docPWObj = $modx->getObject('Docpassword', array(
            'document_id' => $docId
        ));

        if (null !== $docPWObj) { //a password exists for this web page
            $modx->smarty->setTemplatePath($docPasswords->config['smartyPath']);

            $isFormError = false;

            $dbPass = $docPWObj->get('password');

            if (isset($dbPass) && !$docPasswords->isValidSession($docId, $dbPass)) { //no stored password
                if (isset($_POST) && array_key_exists('dPassword', $_POST)
                    && array_key_exists('dSubmit', $_POST)) { //form submitted

                    $formPass = $_POST['dPassword'];

                    if ($formPass == $dbPass) {
                        $docPasswords->setSession($docId, $formPass);
                        break;
                    } else {
                        $isFormError = true;
                    }
                }

                //display incorrect pw message if wrong
                $modx->smarty->assign('style', 'display:none');
                $modx->smarty->assign('message', '');
                if ($isFormError) {
                    $modx->smarty->assign('style', 'display:block');
                    $modx->smarty->assign('message', 'Sorry. The password is incorrect.');
                }

                //password form html
                $content = $modx->smarty->fetch('webPasswordForm.smarty.tpl');
                $resource = $modx->resource;

                $resource->_output = str_replace($resource->content, $content, $resource->_output);

                //reset the smarty template path for MODx
                $modx->smarty->setTemplatePath($modx->getOption('manager_path') . 'templates/default');
            }
        }

        break;

    default:
        break;
}

return;
