<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Machine_Browser extends Light_Database_Object {
   public $id;
   public $test_machine_id;
   public $test_browser_id;
   public $isAvailable;
   public $lastSeen;

   public function init() {
      $this->setSqlTable( 'test_machine_browser' );
      $this->setDbName( 'test' );
   }

}
