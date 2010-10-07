<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Param extends Light_Database_Object
{
   public $id;
   public $testId;
   public $testParamLibraryId;

   public function init()
   {
      $this->setSqlTable('ctm_test_param');
      $this->setDbName('test');
   }

}
