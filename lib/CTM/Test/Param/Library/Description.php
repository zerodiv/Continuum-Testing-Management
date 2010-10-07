<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Param_Library_Description extends Light_Database_Object
{
   public $id;
   public $testParamLibraryId;
   public $description;

   public function init()
   {
      $this->setSqlTable('ctm_test_param_library_description');
      $this->setDbName('test');
   }

}
