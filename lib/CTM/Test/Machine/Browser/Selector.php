<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Machine/Browser.php' );

class CTM_Test_Machine_Browser_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_Test_Machine_Browser' );
   }
}
