<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_Log extends Light_Database_Object {
    
   public $id;
   public $test_run_browser_id;
   public $selenium_log;
   public $run_log;
   public $duration;
   public $createdAt;

   public function init() {
      $this->setSqlTable( 'test_run_log' );
      $this->setDbName( 'test' );
   }

}
