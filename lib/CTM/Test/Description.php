<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Description extends Light_Database_Object
{
   public $id;
   public $testId;
   public $description;

   public function init()
   {
      $this->setSqlTable('ctm_test_description');
      $this->setDbName('test');
   }

}
