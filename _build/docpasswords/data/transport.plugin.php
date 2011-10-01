<?php
/**
 * @package docpasswords
 * @subpackage build
 */

function getPluginContent($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php', '?>'), '', $o));
    return $o;
}

$plugin = $modx->newObject('modPlugin');
$plugin->set('id', 1);
$plugin->set('name', PKG_NAME);
$plugin->set('description', PKG_NAME.' '.PKG_VERSION.'-'.PKG_RELEASE.' plugin for MODx Revolution');
$plugin->set('plugincode', getPluginContent($sources['plugins'] . 'docpasswords.plugin.php'));

return $plugin;
