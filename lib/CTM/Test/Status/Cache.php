<?php

require_once( 'Light/Database/Object/Cache.php' );

class CTM_Test_Status_Cache extends Light_Database_Object_Cache {
   public function init() {
      $this->setObject( 'CTM_Test_Status' );
   }
}
