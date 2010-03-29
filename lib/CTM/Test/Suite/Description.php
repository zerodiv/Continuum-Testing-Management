<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_Description extends Light_Database_Object {
   public $id;
   public $test_suite_id;
   public $description;

   public function init() {
      $this->setSqlTable( 'test_suite_description' );
      $this->setDbName( 'test' );
   }

}
