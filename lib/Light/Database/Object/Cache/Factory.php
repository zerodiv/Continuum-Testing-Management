<?php

class Light_Database_Object_Cache_Factory
{
   private static $_cacheObjects;

   public function &factory( $objectName )
   {

      // init the cache of objects if needed
      if ( empty( self::$_cacheObjects ) ) {
         self::$_cacheObjects = array();
      }

      // if the class isn't included do so
      if ( ! class_exists($objectName) ) {
         $phpFile = str_replace('_', '/', $objectName) . '.php';
         require_once($phpFile);
      }

      // create a new cache
      if ( ! isset( self::$_cacheObjects[ $objectName ] ) ) {
         self::$_cacheObjects[ $objectName ] = new $objectName();
      }

      // return cache 
      return self::$_cacheObjects[ $objectName ];

   }

}
