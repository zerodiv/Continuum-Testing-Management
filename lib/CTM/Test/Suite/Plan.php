<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_Plan extends Light_Database_Object {
   public $id;
   public $testSuiteId;
   public $linked_id;
   public $test_order;
   public $test_suite_plan_type_id;

   public function init() {
      $this->setSqlTable( 'test_suite_plan' );
      $this->setDbName( 'suite' );
   }

}
