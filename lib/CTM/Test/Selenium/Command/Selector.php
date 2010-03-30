<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Selenium/Command.php' );

class CTM_Test_Selenium_Command_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_Test_Selenium_Command' );
   }
}
