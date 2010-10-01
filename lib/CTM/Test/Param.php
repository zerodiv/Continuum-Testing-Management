<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Param extends Light_Database_Object {
   public $id;
   public $testId;
   public $test_param_library_id;

   public function init() {
      $this->setSqlTable( 'test_param' );
      $this->setDbName( 'test' );
   }

}
