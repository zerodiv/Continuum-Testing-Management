<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Suite/Plan/Type.php' );

class CTM_Test_Suite_Plan_Type_Selector extends Light_Database_Selector {
   public function init() {
      $this->setDbObject( 'CTM_Test_Suite_Plan_Type' );
   }
}
