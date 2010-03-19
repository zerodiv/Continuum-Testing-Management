<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Status extends Light_Database_Object {
   public $id;
   public $name;

   public function init() {
      $this->setSqlTable( 'test_status' );
      $this->setDbName( 'test' );
   }

}
