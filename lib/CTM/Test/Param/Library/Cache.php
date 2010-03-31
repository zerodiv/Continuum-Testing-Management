<?php

require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Param/Library/Selector.php' );

class CTM_Test_Param_Library_Cache {
   private $_cache;

   function __construct() {
      $this->_cache = array();
   }

   public function getByName( $name ) {
      // iterate across the cache
      foreach ( $this->_cache as $cached ) {
         if ( $cached->name == $name ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Param_Library_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'name', '=', $name ),
         );
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $this->_cache[] = $rows[0];
            return $rows[0];
         }

         // not found.
         return null;

      } catch ( Exception $e ) {
         throw $e;
      }

      // not found
      return null;
   }

   public function getById( $id ) {
      // iterate across the cache
      foreach ( $this->_cache as $cached ) {
         if ( $cached->id == $id ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Param_Library_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $this->_cache[] = $rows[0];
            return $rows[0];
         }

         // not found.
         return null;

      } catch ( Exception $e ) {
         throw $e;
      }

      // not found
      return null;
   }

}
