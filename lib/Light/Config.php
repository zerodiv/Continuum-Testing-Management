<?php

require_once( 'Light/Config/Object.php' );

// mini-factory esq interface to make a static callable interface for the config system.
class Light_Config
{
   static private $_configObj;

   public static function get( $namespace, $variable )
   {
      if ( ! isset( Light_Config::$_configObj ) ) {
         Light_Config::$_configObj = new Light_Config_Object();
      }
      return Light_Config::$_configObj->get($namespace, $variable);
   }
}
