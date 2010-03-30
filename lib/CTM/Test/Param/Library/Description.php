<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Param_Library_Description extends Light_Database_Object {
   public $id;
   public $test_param_library_id;
   public $description;

   public function init() {
      $this->setSqlTable( 'test_param_library_description' );
      $this->setDbName( 'test' );
   }

}
