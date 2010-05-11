<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_Log extends Light_Database_Object {
    
   public $id;
   public $test_run_browser_id;
   public $data;
   public $duration;
   public $created_at;

   public function init() {
      $this->setSqlTable( 'test_run_log' );
      $this->setDbName( 'test' );
   }

}
