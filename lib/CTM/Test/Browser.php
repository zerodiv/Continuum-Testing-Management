<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Browser extends Light_Database_Object
{
   public $id;
   public $name;
   public $majorVersion;
   public $minorVersion;
   public $patchVersion;
   public $isAvailable;
   public $lastSeen;

   public function init()
   {
      $this->setSqlTable('ctm_test_browser');
      $this->setDbName('test');
   }

   public function getPrettyName()
   {
      if ( $this->name == 'firefox' ) {
         return 'FireFox';
      }
      if ( $this->name == 'iexplore' ) {
         return 'Internet Explorer';
      }
      if ( $this->name == 'googlechrome' ) {
         return 'Google Chrome';
      }
      if ( $this->name == 'safari' ) {
         return 'Safari';
      }
      if ( $this->name == 'iphone' ) {
         return 'iPhone';
      }
      if ( $this->name == 'android' ) {
         return 'Google Android';
      }
   }

}
