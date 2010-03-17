<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite extends Light_Database_Object {
   public $id;
   public $test_folder_id;
   public $name;
   public $description;
   public $created_at;
   public $modified_at;
   public $modified_by;

   public function init() {
      $this->setSqlTable( 'test_suite' );
      $this->setDbName( 'suite' );
   }

}
