<?php
/**
 * @package docpasswords
 * @subpackage build
 *
 * @events
 * OnDocFormPrerender, OnDocFormRender, OnDocFormSave, OnDocFormDelete
 *  OnWebPageInit, OnWebPagePrerender, OnWebPageComplete
 */


$events = array();

$events['OnDocFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnDocFormPrerender']->fromArray(array(
    'event' => 'OnDocFormPrerender',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnDocFormRender'] = $modx->newObject('modPluginEvent');
$events['OnDocFormRender']->fromArray(array(
    'event' => 'OnDocFormRender',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnDocFormSave'] = $modx->newObject('modPluginEvent');
$events['OnDocFormSave']->fromArray(array(
    'event' => 'OnDocFormSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnDocFormDelete'] = $modx->newObject('modPluginEvent');
$events['OnDocFormDelete']->fromArray(array(
    'event' => 'OnDocFormDelete',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnWebPageInit'] = $modx->newObject('modPluginEvent');
$events['OnWebPageInit']->fromArray(array(
    'event' => 'OnWebPageInit',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnWebPagePrerender'] = $modx->newObject('modPluginEvent');
$events['OnWebPagePrerender']->fromArray(array(
    'event' => 'OnWebPagePrerender',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnWebPageComplete'] = $modx->newObject('modPluginEvent');
$events['OnWebPageComplete']->fromArray(array(
    'event' => 'OnWebPageComplete',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

return $events;
