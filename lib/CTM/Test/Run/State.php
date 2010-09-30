<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_State extends Light_Database_Object {

    const STATE_QUEUED = 1;
    const STATE_EXECUTING = 2;
    const STATE_COMPLETED = 3;
    const STATE_ARCHIVED = 4;
    const STATE_FAILED = 5;

   public $id;
   public $name;
   public $description;

   public function init() {
      $this->setSqlTable( 'test_run_state' );
      $this->setDbName( 'test' );
   }

}
