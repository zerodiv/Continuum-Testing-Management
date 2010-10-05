<?php

abstract class Light_Database_Object_Cache
{
   private $_cache;
   private $_object;
   private $_selObject;
   private $_sqlIdField;
   private $_sqlFields;

   function __construct()
   {
      $this->_cache = array();
      $this->init();
   }

   public function &getCache()
   {
      return $this->_cache;
   }

   public function init()
   {
      throw new Exception( 'init should be overwritten with a client side impl' );
   }

   public function setObject( $name )
   {
      if ( ! class_exists($name) ) {
         $filename = str_replace('_', '/', $name) . '.php';
         require_once($filename);
      }
      $selName = $name . '_Selector';
      if ( ! class_exists($selName) ) {
         $filename = str_replace('_', '/', $selName) . '.php';
         require_once($filename);
      }

      // okay now that we have the objects loaded up pull in our data we need to work with.
      $obj = new $name();

      $this->_sqlIdField = $obj->getIdField();
      $this->_sqlFields = $obj->getFieldNames();
      $this->_object = $name;
      $this->_selObject = $selName;

   }

   private function _getByField( $fieldName, $value )
   {
      foreach ( $this->_cache as $cached ) {
         if ( isset( $cached->$fieldName ) && $cached->$fieldName == $value ) {
            return $cached;
         }
      }
      try {
         $selObject = $this->_selObject;
         $sel = new $selObject();
         $andParams = array(
               new Light_Database_Selector_Criteria($fieldName, '=', $value)
         );
         $rows = $sel->find($andParams);
         if ( isset( $rows[0] ) ) {
            $this->_cache[] = $rows[0];
            return $rows[0];
         }
         // not found.
         return null;
      } catch ( Exception $e ) {
      }
   }

   public function __call( $method, $args )
   {
      if ( preg_match('/^getBy(.*)$/i', $method, $methodPregs) ) {
         $fieldName = strtolower($methodPregs[1]);
         if ( in_array($fieldName, $this->_sqlFields) ) {
            return $this->_getByField($fieldName, $args[0]);
         }
      }
      return;
   }

}
