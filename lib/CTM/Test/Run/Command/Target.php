<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_Command_Target extends Light_Database_Object {
   public $id;
   public $test_run_command_id;
   public $target;

   public function init() {
      $this->setSqlTable( 'test_run_command_target' );
      $this->setDbName( 'test' );
   }

}
