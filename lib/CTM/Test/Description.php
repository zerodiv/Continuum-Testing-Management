<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Description extends Light_Database_Object {
   public $id;
   public $test_id;
   public $description;

   public function init() {
      $this->setSqlTable( 'test_description' );
      $this->setDbName( 'test' );
   }

}
