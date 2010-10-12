<?php

// --------------------------------------------------------------------------------
// Default boostrap.php
//
// This file configures the php environment to include our include path. If your php
// environment does not allow modifying the include path at runtime you will need to 
// modify your php.ini include_path settings accordingly.
// --------------------------------------------------------------------------------
$includePath = get_include_path();
set_include_path(dirname(__FILE__) . '/lib:' . $includePath);

// test to see if we have configuration files.
$configFiles = array(
   dirname(__FILE__) . '/etc/config.ini',
   dirname(__FILE__) . '/etc/db.ini'
);

$missingConfigFile = false;
foreach ( $configFiles as $configFile ) {
   // echo "configFile: $configFile\n";
   if ( ! file_exists($configFile) ) {
      $missingConfigFile = true;
   }
}

$installerDone = false;
if ( file_exists( dirname(__FILE__) . '/etc/installer.done' ) ) {
   $installerDone = true;
}

// echo "missingConfigFile: $missingConfigFile installerDone: $installerDone\n";

if (
      $missingConfigFile == true || 
      $installerDone == false 
      // JEO: This is for my internal testing you shouldn't need to re-run this this way in production.
      // || (isset($_GET['installerTesting']) && $_GET['installerTesting'] == 1)
   ) {
   require_once( 'CTM/Installer.php' );
   $ctmInstallerObj = new CTM_Installer();
   $ctmInstallerObj->displayPage();
   exit();
}
