<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Revision/Framework.php' );

class CTM_Test_Revision extends Light_Database_Object
{
   public $id;
   public $testId;
   public $modifiedAt;
   public $modifiedBy;
   public $revisionId;

   public function init()
   {
      $this->setSqlTable('ctm_test_revision');
      $this->setDbName('test');
      $this->addOneToOneRelationship('ModifiedBy', 'CTM_User', 'modifiedBy', 'id');
   }

}
