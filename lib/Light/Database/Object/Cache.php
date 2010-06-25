<?php

abstract class Light_Database_Object_Cache {
   private $_cache;
   private $_object;
   private $_sel_object;
   private $_sql_id_field;
   private $_sql_fields;

   function __construct() {
      $this->_cache = array();

      $this->init();

   }

   public function init() {
      throw new Exception( 'init should be overwritten with a client side impl' );
   }

   public function setObject( $name ) {
      $sel_name = $name . '_Selector';
      if ( ! class_exists( $sel_name ) ) {
         $filename = $sel_name;
         $filename = str_replace( '_', '/', $filename );
         require_once( $filename );
      }
      if ( ! class_exists( $name ) ) {
         $filename = $name;
         $filename = str_replace( '_', '/', $filename );
         require_once( $filename );
      }

      // okay now that we have the objects loaded up pull in our data we need to work with.
      $obj = new $name();

      $this->_sql_id_field = $obj->getIdField();
      $this->_sql_fields = $obj->getFieldNames();
      $this->_object = $name;
      $this->_sel_object = $sel_name;

   }

   private function _getByField( $field_name, $value ) {
      foreach ( $this->_cache as $cached ) {
         if ( isset( $cached->$field_name ) && $cached->$field_name == $value ) {
            return $cached;
         }
      }
      try {
         $sel_object = $this->_sel_object;
         $sel = new $sel_object();
         $and_params = array(
               new Light_Database_Selector_Criteria( $field_name, '=', $value ),
         );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            $this->_cache[] = $rows[0];
            return $rows[0];
         }
         // not found.
         return null;
      } catch ( Exception $e ) {
      }
   }

   public function __call( $method, $args ) {
      if ( preg_match( '/^getBy(.*)$/i', $method, $methodPregs ) ) {
         $fieldName = strtolower( $methodPregs[1] );
         if ( in_array( $fieldName, $this->_sql_fields ) ) {
            return $this->_getByField( $fieldName, $args[0] );
         }
      }
      return;
   }

}
