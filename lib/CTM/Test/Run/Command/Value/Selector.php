<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/Test/Run/Command/Value.php' );

class CTM_Test_Run_Command_Value_Selector extends Light_Database_Selector
{
   public function init()
   {
      $this->setDbObject('CTM_Test_Run_Command_Value');
   }
}
