<?php

require_once('Light/Database/Connection/Object.php');

class Light_Database_Connection_Factory
{
   static private $_dbFactoryObj;

   static public function getDBH( $name, $pooled = true )
   {
      if ( ! isset(Light_Database_Connection_Factory::$_dbFactoryObj)) {
         Light_Database_Connection_Factory::$_dbFactoryObj = new Light_Database_Connection_Object();
      }
      return Light_Database_Connection_Factory::$_dbFactoryObj->getDBH($name, $pooled);
   }

}
