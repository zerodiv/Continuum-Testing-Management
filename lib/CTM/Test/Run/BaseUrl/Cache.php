<?php

require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Run/BaseUrl/Selector.php' );

class CTM_Test_Run_BaseUrl_Cache {
   private $_cache;

   function __construct() {
      $this->_cache = array();
   }

   public function getByCompoundKey( $test_run_id, $test_suite_id, $test_id ) {
      // iterate across the cache
      foreach ( $this->_cache as $cached ) {
         if ( $cached->test_run_id == $test_run_id && $cached->test_suite_id == $test_suite_id && $cached->test_id == $test_id ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Run_BaseUrl_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run_id ),
               new Light_Database_Selector_Criteria( 'test_suite_id', '=', $test_suite_id ),
               new Light_Database_Selector_Criteria( 'test_id', '=', $test_id ),
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
         $sel = new CTM_Test_Run_BaseUrl_Selector();
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
