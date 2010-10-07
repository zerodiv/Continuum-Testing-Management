<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_Description extends Light_Database_Object
{
   public $id;
   public $testSuiteId;
   public $description;

   public function init()
   {
      $this->setSqlTable('ctm_test_suite_description');
      $this->setDbName('test');
   }

}
