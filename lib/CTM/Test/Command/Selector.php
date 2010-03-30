<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Command.php' );

class CTM_Test_Command_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_Test_Command' );
   }
}
