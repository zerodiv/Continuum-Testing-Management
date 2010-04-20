<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_Browser extends Light_Database_Object {
   public $id;
   public $test_run_id;
   public $test_browser_id;
   public $test_machine_id;
   public $test_run_state_id;

   public function init() {
      $this->setSqlTable( 'test_run_browser' );
      $this->setDbName( 'test' );
   }

}
