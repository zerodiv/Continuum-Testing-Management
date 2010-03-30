<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Param_Library extends Light_Database_Object {
   public $id;
   public $name;
   public $created_at;
   public $created_by;
   public $modified_at;
   public $modified_by;

   public function init() {
      $this->setSqlTable( 'test_param_library' );
      $this->setDbName( 'test' );
   }

}
