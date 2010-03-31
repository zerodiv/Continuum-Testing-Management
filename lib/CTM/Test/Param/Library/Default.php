<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Param_Library_Default extends Light_Database_Object {
   public $id;
   public $test_param_library_id;
   public $default_value;

   public function init() {
      $this->setSqlTable( 'test_param_library_default_value' );
      $this->setDbName( 'test' );
   }

}
