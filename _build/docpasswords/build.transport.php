<?php

/**
 * DocPasswords build script
 *
 * @package docpasswords
 * @subpackage build
 */
error_reporting(8191);
ini_set('display_errors', true);

$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

/* define package names */
define('PKG_NAME', 'DocPasswords');
define('PKG_NAME_LOWER', 'docpasswords');
define('PKG_VERSION', '1.2');
define('PKG_RELEASE', 'rc1');

/* define build paths */
$devRoot = realpath(dirname(dirname(__FILE__)) . '/../') . '/';
$buildRoot = dirname(__FILE__) . '/';

$sources = array(
    'root' => $devRoot,
    'build' => $buildRoot,
    'data' => $buildRoot . 'data/',
    'properties' => $buildRoot . 'data/properties/',
    'resolvers' => $buildRoot . 'resolvers/',
    'docs' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'elements' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/elements/',
    'chunks' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/elements/chunks/',
    'smarty' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/elements/smarty/',
    'snippets' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'plugins' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/elements/plugins/',
    'lexicon' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'source_core' => $devRoot . 'core/components/' . PKG_NAME_LOWER . '/',
    'source_assets' => $devRoot . 'assets/components/' . PKG_NAME_LOWER . '/',
);
unset($devRoot, $buildRoot);

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . '../build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_DEBUG);
echo XPDO_CLI_MODE ? '' : '<pre>';
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');
$modx->log(modX::LOG_LEVEL_INFO,'Created Transport Package and Namespace.');

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category',PKG_NAME);

/* add plugins */
$modx->log(modX::LOG_LEVEL_DEBUG, 'Packaging in plugins...');
$plugin = include $sources['data'] . 'transport.plugin.php';
if (empty($plugin)) $modx->log(modX::LOG_LEVEL_INFO, 'Could not package in plugins.');

/* add plugin events */
$modx->log(modX::LOG_LEVEL_DEBUG, 'Packaging in plugin events...');
$events = include $sources['data'] . 'transport.pluginevents.php';

if (is_array($events) && !empty($events)) {
    $plugin->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_DEBUG, 'Packaged in ' . count($events) . ' Plugin Events.');
    flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_INFO, 'Could not find plugin events!');
}

unset($events);
$attributes = array (
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
);
$vehicle = $builder->createVehicle($plugin, $attributes);
$builder->putVehicle($vehicle);
unset($vehicle,$attributes,$plugin);

/* create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Children' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'category',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
                'Plugins' => array(
                    xPDOTransport::PRESERVE_KEYS => false,
                    xPDOTransport::UPDATE_OBJECT => true,
                    xPDOTransport::UNIQUE_KEY => 'name',
                )
            ),
        ),
        'Plugins' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        )
    ),
);
$vehicle = $builder->createVehicle($category,$attr);

/* file resolvers */
$modx->log(modX::LOG_LEVEL_DEBUG, 'Adding file resolvers to category...');
$vehicle->resolve('file', array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

/* init db tables */
$modx->log(modX::LOG_LEVEL_DEBUG, 'Adding in PHP Resolvers...');
$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'resolve.tables.php',
));

$builder->putVehicle($vehicle);
unset($vehicle, $menu);


/* pack in the license file, readme and setup options */
$modx->log(modX::LOG_LEVEL_DEBUG, 'Adding package attributes and setup options...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    //'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => array(
        'source' => $sources['build'] . 'setup.options.php',
    ),
));

/* zip up package */
$modx->log(modX::LOG_LEVEL_DEBUG, 'Packing up transport package zip...');
$builder->pack();

$tend = explode(" ", microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf("%2.4f s", ($tend - $tstart));
$modx->log(modX::LOG_LEVEL_DEBUG, "Package Built. Execution time: {$totalTime}\n");
exit();
?>
