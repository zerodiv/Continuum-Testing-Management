<?php

require_once( 'Light/Database/Object/Cache.php' );

class CTM_Test_Machine_Browser_Cache extends Light_Database_Object_Cache {
   private $_cache;

   public function init() {
      $this->setObject( 'CTM_Test_Machine_Browser' );
   }

}
