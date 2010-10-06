<?php

require_once( 'Light/Database/Object/Cache.php' );

class CTM_Test_Run_BaseUrl_Cache extends Light_Database_Object_Cache
{

   public function init()
   {
      $this->setObject('CTM_Test_Run_BaseUrl');
   }

   public function getByCompoundKey( $testRunId, $test_suite_id, $testId )
   {
      // iterate across the cache
      $_cache = $this->getCache();
      foreach ( $_cache as $cached ) {
         if ( $cached->testRunId == $testRunId && $cached->test_suite_id == $test_suite_id && $cached->testId == $testId ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Run_BaseUrl_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'testRunId', '=', $testRunId ),
               new Light_Database_Selector_Criteria( 'test_suite_id', '=', $test_suite_id ),
               new Light_Database_Selector_Criteria( 'testId', '=', $testId ),
         );
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $_cache[] = $rows[0];
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
