<?php
/**
 * Build the setup options form.
 *
 * @package docpasswords
 * @subpackage build
 */

/* set some default values */

/* get values based on mode */
$output = '';
switch ($options[xPDOTransport::PACKAGE_ACTION])
{
case xPDOTransport::ACTION_INSTALL:
    $output =
        '<h2>DocPasswords Installer</h2>
        <p>Thanks for installing the DocPasswords Manager! Please review the setup options below before proceeding.</p><br />';
    break;
case xPDOTransport::ACTION_UPGRADE:
    $output =
        '<h2>DocPasswords Upgrade</h2>
        <p>The DocPasswords Manager will be upgraded! Please review the setup options below before proceeding.</p><br />';
    break;
case xPDOTransport::ACTION_UNINSTALL:
    $output =
        '<h2>DocPasswords UnInstaller</h2>
        <p>Are you sure you want to uninstall the DocPasswords Manager.</p><br />';
    break;
    break;
}
return $output;
