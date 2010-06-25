<?php

require_once( 'Light/Database/Object.php' );

class CTM_User_Role extends Light_Database_Object {
   public $id;
   public $name;

   public function init() {
      $this->setSqlTable( 'account_role' );
      $this->setDbName( 'account' );
   }

}
