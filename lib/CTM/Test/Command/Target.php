<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Command_Target extends Light_Database_Object {
   public $id;
   public $test_command_id;
   public $target;

   public function init() {
      $this->setSqlTable( 'test_command_target' );
      $this->setDbName( 'test' );
   }

}
