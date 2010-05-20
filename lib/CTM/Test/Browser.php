<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Browser extends Light_Database_Object {
   public $id;
   public $name;
   public $major_version;
   public $minor_version;
   public $patch_version;
   public $is_available;
   public $last_seen;

   public function init() {
      $this->setSqlTable( 'test_browser' );
      $this->setDbName( 'test' );
   }

   public function getPrettyName() {
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
   }

}
