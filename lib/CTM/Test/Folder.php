<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Folder extends Light_Database_Object {
   public $id;
   public $parent_id;
   public $name;

   public function init() {
      $this->setSqlTable( 'test_folder' );
      $this->setDbName( 'folder' );
   }

}
