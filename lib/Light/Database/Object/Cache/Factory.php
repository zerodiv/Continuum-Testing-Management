<?php

class Light_Database_Object_Cache_Factory {
   private static $_cache_objects;

   public function &factory( $object_name ) {

      // init the cache of objects if needed
      if ( empty( self::$_cache_objects ) ) {
         self::$_cache_objects = array();
      }

      // if the class isn't included do so
      if ( ! class_exists( $object_name ) ) {
         $php_file = str_replace( '_', '/', $object_name ) . '.php';
         require_once( $php_file );
      }

      // create a new cache
      if ( ! isset( self::$_cache_objects[ $object_name ] ) ) {
         self::$_cache_objects[ $object_name ] = new $object_name();
      }

      // return cache 
      return self::$_cache_objects[ $object_name ];

   }

}
