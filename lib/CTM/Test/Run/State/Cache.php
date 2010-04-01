<?php

require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Run/State/Selector.php' );

class CTM_Test_Run_State_Cache {
   private $_cache;

   function __construct() {
      $this->_cache = array();
   }

   public function getById( $id ) {
      // iterate across the cache
      foreach ( $this->_cache as $cached ) {
         if ( $cached->id == $id ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Run_State_Selector();
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
