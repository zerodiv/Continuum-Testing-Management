<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Revision/Framework.php' );

class CTM_Test_Suite_Revision extends Light_Database_Object {
   public $id;
   public $test_suite_id;
   public $modified_at;
   public $modified_by;
   public $revision_id;

   public function init() {
      $this->setSqlTable( 'test_suite_revision' );
      $this->setDbName( 'test' );
   }

}
