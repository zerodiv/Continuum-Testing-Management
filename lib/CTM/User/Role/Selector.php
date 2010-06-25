<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/User/Role.php' );

class CTM_User_Role_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_User_Role' );
   }
}
