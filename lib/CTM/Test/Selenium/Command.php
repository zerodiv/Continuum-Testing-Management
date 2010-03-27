<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test extends Light_Database_Object {
   public $id;
   public $test_folder_id;
   public $name;
   public $test_status_id;
   public $created_at;
   public $created_by;
   public $modified_at;
   public $modified_by;

   public function init() {
      $this->setSqlTable( 'test' );
      $this->setDbName( 'test' );
   }

}
