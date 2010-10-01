<?php

require_once( 'Light/Database/Object.php' );

class CTM_User_Role extends Light_Database_Object
{
   public $id;
   public $name;

   public function init()
   {
      $this->setSqlTable('ctm_user_role');
      $this->setDbName('user');
   }

}
