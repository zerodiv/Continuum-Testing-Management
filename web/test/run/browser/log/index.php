<?php

require_once( '../../../../../bootstrap.php' ); require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Log/Selector.php' );

class CTM_Site_Test_Run_Browser_Log extends CTM_Site { 
   public function setupPage() { 
      return true; 
   }

   public function handleRequest() { 
      
      $testRunBrowserId = $this->getOrPost('testRunBrowserId', '');
      $type = $this->getOrPost( 'type', 'run' ); 
      
      try { 
         if ( $type == 'selenium' ) {
            // header( 'Content-Type: text/html' );
         } else {
            header( 'Content-Type: text/plain' );
         }

         if ( $testRunBrowserId > 0 ) { 
            $test_run_log_sel = new CTM_Test_Run_Log_Selector();
            $and_params = array(
                  new Light_Database_Selector_Criteria('testRunBrowserId', '=', $testRunBrowserId)
            );
            
            $test_run_logs = $test_run_log_sel->find( $and_params ); 
            
            if (!empty($test_run_logs)) { 
               foreach ($test_run_logs as $test_run_log) { 
                  if ( $type == 'selenium' ) {
                     echo stripslashes($test_run_log->selenium_log);
                  } else {
                     echo $test_run_log->run_log;
                  }
               } 
            } else {
               echo 'Invalid id.';
            } 
         
         } else {
            echo 'No id provided';
         } 
      } catch ( Exception $e ) {
         echo 'Unable to find test browser run by id provided';
      } 
      return false;
   } 
   
   public function displayBody() {
      return true;
   } 

}

$test_add_obj = new CTM_Site_Test_Run_Browser_Log();
$test_add_obj->displayPage();
