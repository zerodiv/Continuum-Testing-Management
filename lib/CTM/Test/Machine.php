<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Machine extends Light_Database_Object {
   public $id;
   public $hostname;
   public $os;
   public $created_at;
   public $last_modified;
   public $is_disabled;

   public function init() {
      $this->setSqlTable( 'test_machine' );
      $this->setDbName( 'test' );
   }

}
