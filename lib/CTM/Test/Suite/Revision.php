<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Revision/Framework.php' );

class CTM_Test_Suite_Revision extends Light_Database_Object
{
   public $id;
   public $testSuiteId;
   public $modifiedAt;
   public $modifiedBy;
   public $revisionId;

   public function init()
   {
      $this->setSqlTable('test_suite_revision');
      $this->setDbName('test');
   }

}
