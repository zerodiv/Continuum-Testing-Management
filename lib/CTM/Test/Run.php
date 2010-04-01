<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run extends Light_Database_Object {
   public $id;
   public $test_suite_id;
   public $test_run_status_id;
   public $iterations;
   public $created_at;
   public $created_by;

   public function init() {
      $this->setSqlTable( 'test_run' );
      $this->setDbName( 'test' );
   }

}
