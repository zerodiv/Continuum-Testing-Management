<?php

require_once( 'Light/Database/Selector.php' );
require_once( 'CTM/User.php' );

class CTM_User_Selector extends Light_Database_Selector
{
   public function init()
   {
      $this->setDbObject('CTM_User');
   }
}
