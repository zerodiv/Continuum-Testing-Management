<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_State extends Light_Database_Object {
   public $id;
   public $name;

   public function init() {
      $this->setSqlTable( 'test_run_state' );
      $this->setDbName( 'test' );
   }

}
