<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Command extends Light_Database_Object {
   public $id;
   public $test_id;
   public $test_selenium_command_id;
   public $target;
   public $value;

   public function init() {
      $this->setSqlTable( 'test_command' );
      $this->setDbName( 'test' );
   }

}
