<?php

require_once( 'Light/Database/Object/Cache.php' );

class CTM_Test_Run_BaseUrl_Cache extends Light_Database_Object_Cache
{

   public function init()
   {
      $this->setObject('CTM_Test_Run_BaseUrl');
   }

   public function getByCompoundKey( $testRunId, $testSuiteId, $testId )
   {
      // iterate across the cache
      $_cache = $this->getCache();
      foreach ( $_cache as $cached ) {
         if ( $cached->testRunId == $testRunId && $cached->testSuiteId == $testSuiteId && $cached->testId == $testId ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Run_BaseUrl_Selector();
         $andParams = array(
               new Light_Database_Selector_Criteria( 'testRunId', '=', $testRunId ),
               new Light_Database_Selector_Criteria( 'testSuiteId', '=', $testSuiteId ),
               new Light_Database_Selector_Criteria( 'testId', '=', $testId ),
         );
         $rows = $sel->find($andParams);

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
