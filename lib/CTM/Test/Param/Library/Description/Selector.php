<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Param/Library/Description.php' );

class CTM_Test_Param_Library_Description_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_Test_Param_Library_Description' );
   }
}
