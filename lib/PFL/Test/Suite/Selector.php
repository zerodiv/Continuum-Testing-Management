<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'PFL/Test/Suite.php' );

class PFL_Test_Suite_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'PFL_Test_Suite' );
   }
}
