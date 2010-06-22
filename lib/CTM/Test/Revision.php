<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Revision/Framework.php' );

class CTM_Test_Revision extends Light_Database_Object {
   public $id;
   public $test_id;
   public $modified_at;
   public $modified_by;
   public $revision_id;

   public function init() {
      $this->setSqlTable( 'test_revision' );
      $this->setDbName( 'test' );
   }

}
