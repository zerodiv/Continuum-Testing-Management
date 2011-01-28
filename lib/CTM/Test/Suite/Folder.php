<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_Folder extends Light_Database_Object
{
   public $id;
   public $parentId;
   public $name;

   public function init()
   {
      $this->setSqlTable('ctm_test_suite_folder');
      $this->setDbName('folder');
   }

}
