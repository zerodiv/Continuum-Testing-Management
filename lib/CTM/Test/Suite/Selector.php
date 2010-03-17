<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Suite.php' );

class CTM_Test_Suite_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_Test_Suite' );
   }
}
