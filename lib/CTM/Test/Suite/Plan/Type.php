<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_Plan_Type extends Light_Database_Object {
   public $id;
   public $name;

   public function init() {
      $this->setSqlTable( 'test_suite_plan_type' );
      $this->setDbName( 'suite' );
   }

}
