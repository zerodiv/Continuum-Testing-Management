<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_Plan extends Light_Database_Object
{
   public $id;
   public $testSuiteId;
   public $linkedId;
   public $testOrder;
   public $testSuitePlanTypeId;

   public function init()
   {
      $this->setSqlTable('ctm_test_suite_plan');
      $this->setDbName('suite');
   }

}
