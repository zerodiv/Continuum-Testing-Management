<?php

require_once( 'Light/Database/Cache.php' );

class CTM_User_Role_Cache extends Light_Database_Cache {
   public function init() {
      $this->setObject( 'CTM_User_Role' );
   }
}
