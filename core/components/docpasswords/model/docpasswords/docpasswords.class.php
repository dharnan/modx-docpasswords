<?php

/**
 * @package docpasswords
 *
 * @author     Arietis Software <code@arietis-software.com>
 * @copyright  Copyright (c) 2011 Arietis Software Innovations
 * @license    http://www.software.com/license/gnu/license.txt   GNU License Version 3
 */
class Docpasswords
{

    public $modx;
    public $config = array();
    public $sessionNamespace = 'Docpassword';

    /**
     *
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx = &$modx;

        $baseAssetsUrl = $this->modx->getOption('assets_url') . 'components/docpasswords/';
        $basePath = $this->modx->getOption('dev.core_path', $config, $this->modx->getOption('core_path')) . 'components/docpasswords/';
        $assetsUrl = $this->modx->getOption('dev.assets_url', $config, $this->modx->getOption('assets_url')) . 'components/docpasswords/';
        $this->config = array_merge(array(
                    'basePath'          =>  $basePath,
                    'corePath'          =>  $basePath,
                    'modelPath'         =>  $basePath . 'model/',
                    'processorsPath'    =>  $basePath . 'processors/',
                    'chunksPath'        =>  $basePath . 'elements/chunks/',
                    'smartyPath'        =>  $basePath . 'elements/smarty/',
                    'jsUrl'             =>  $assetsUrl . 'js/',
                    'cssUrl'            =>  $assetsUrl . 'css/',
                    'assetsUrl'         =>  $assetsUrl,
                    //'connectorUrl'      =>  $baseAssetsUrl . 'connector.php',
                        ), $config);

        $this->modx->addPackage('docpasswords', $this->config['modelPath']);
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name, $properties = array())
    {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->_getTplChunk($name);
            if (empty($chunk)) {
                $chunk = $this->modx->getObject('modChunk', array('name' => $name));
                if ($chunk == false)
                    return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.$postfix
     * @param string $postfix The default postfix to search for chunks at.
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name, $postfix = '.chunk.tpl')
    {
        $chunk = false;
        $f = $this->config['chunksPath'] . strtolower($name) . $postfix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name', $name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    /**
     *
     * @param <type> $docId
     * @param <type> $pass
     * @return <type>
     */
    public function isValidSession($docId, $pass)
    {
        $session = unserialize($_SESSION['Docpassword']);

        if (!isset($session)) {
            return false;
        }

        if (is_array($session)) {
            foreach ($session as $array) {
                if (isset($array['i']) && $array['i'] == $docId && isset($array['t'])) {
                    $now = strtotime("now");
                    if (isset($array['p']) && $array['p'] == $pass
                            && (($now-$array['t'])/3600 <= 2)) { //2hr remember me session
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        return false;
    }

    /**
     *
     * @param <type> $docId
     * @param <type> $pass
     */
    public function setSession($docId, $pass)
    {
        
        
        $session = unserialize($_SESSION[$this->sessionNamespace]);

        if (empty($session)) {
            $session = array();
        }

        //look for existing docId
        $foundExistingDocId = false;
        foreach ($session as &$arr) {
            if ($arr['i'] == $docId) { //found existing docId
                $arr['p'] = $pass; //update pass
                $arr['t'] = strtotime("now"); //update unix time
                $foundExistingDocId = true;
            }
        }

        if (!$foundExistingDocId) { //no existing docId
            $sessionArr = array(
                'i' => $docId, //id
                'p' => $pass, //pass
                't' => strtotime("now") //unix time
            );
            array_push($session, $sessionArr); //create new
        }

        $_SESSION[$this->sessionNamespace] = serialize($session);
    }

}