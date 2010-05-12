<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Cache.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Run/State/Cache.php' );
require_once( 'CTM/Test/Machine/Cache.php' );
require_once( 'CTM/Test/Browser/Cache.php' );

class CTM_Site_Test_Runs extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Runs';
      return true;
   }

   public function handleRequest() {
      $action = $this->getOrPost( 'action', '' );
      $test_run_id = $this->getOrPost( 'test_run_id', '' );

      $this->requiresAuth();

      if ( $action == 'remove_test_run' ) {
         // try to find the target test_run
         try {
            $sel = new CTM_Test_Run_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run_id ) );
            $test_runs = $sel->find( $and_params );
            if ( isset( $test_runs[0] ) ) {
               $test_run = $test_runs[0];
               $test_run->remove();
            }
         } catch ( Exception $e ) {
            // we failed to delete.
            return true;
         }
      }

      return true;
   }

   public function displayBody() {
      $test_run_state_id = $this->getOrPost( 'test_run_state_id', 1 );

      $run_state_cache = new CTM_Test_Run_State_Cache();
      $user_cache = new CTM_User_Cache();
      $test_machine_cache = new CTM_Test_Machine_Cache();
      $test_browser_cache = new CTM_Test_Browser_Cache();

      $queued_state = $run_state_cache->getById( 1 );
      $executing_state = $run_state_cache->getById( 2 );
      $completed_state = $run_state_cache->getById( 3 );
      $archived_state = $run_state_cache->getById( 4 );
      $failed_state = $run_state_cache->getById(5);

      // we should have a cached value for this.
      $run_state = $run_state_cache->getById( $test_run_state_id );

      if ( ! isset( $run_state->id ) ) {
         // load up the queued page if we cannot find the current run state.
         $run_state = $run_state_cache->getById( 1 );
      }

      $test_runs = null;
      try {
         $sel = new CTM_Test_Run_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_run_state_id', '=', $run_state->id ) );
         $test_runs = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<div class="aiTopNav">' );
      $this->printHtml( '<ul class="basictab">' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/?test_run_state_id=' . $queued_state->id . '">' . ucfirst( $queued_state->name ) . ' Runs</li>' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/?test_run_state_id=' . $executing_state->id . '">' . ucfirst( $executing_state->name ) . ' Runs</li>' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/?test_run_state_id=' . $completed_state->id . '">' . ucfirst( $completed_state->name ) . ' Runs</li>' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/?test_run_state_id=' . $archived_state->id . '">' . ucfirst( $archived_state->name ) . ' Runs</li>' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/?test_run_state_id=' . $failed_state->id . '">' . ucfirst( $failed_state->name ) . ' Runs</li>' );
      $this->printHtml( '</ul>' );
      $this->printHtml( '</div>' );

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="6">' . ucfirst($run_state->name) . ' Runs</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td class="aiColumnOne">ID</td>' );
      $this->printHtml( '<td>Test Suite</td>' );
      $this->printHtml( '<td>Iterations</td>' );
      $this->printHtml( '<td>Created At</td>' );
      $this->printHtml( '<td>Created By</td>' );
      $this->printHtml( '<td>Actions</td>' );
      $this->printHtml( '</tr>' );

      if ( count( $test_runs ) == 0 ) {
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="6"><center> - There are no test runs in a ' . ucfirst( $run_state->name ) . ' state.</center></td>' );
         $this->printHtml( '</tr>' );
      } else {
         foreach ( $test_runs as $test_run ) {
            $class = $this->oddEvenClass();
            $created_by = $user_cache->getById( $test_run->created_by );

            // lookup the test suite that is associated to this test run.
            $test_suite = null;
            try {
               $sel = new CTM_Test_Suite_Selector();
               $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run->test_suite_id ) );
               $test_suites = $sel->find( $and_params );
               if ( isset( $test_suites[0] ) ) {
                  $test_suite = $test_suites[0];
               }
            } catch ( Exception $e ) {
            }

            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td class="aiColumnOne">' . $test_run->id . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
            $this->printHtml( '<td>' . $test_run->iterations . '</td>' );
            $this->printHtml( '<td>' . $this->formatDate( $test_run->created_at ) . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $created_by->username ) . '</td>' );
            $this->printHtml( '<td><center>' );
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/run/download/?id=' . $test_run->id . '" class="ctmButton">Download</a>' );
            // while a test is executing we cannot do any admin actions to it.
            if ( $test_run->test_run_state_id == $completed_state->id ) {
               $this->printHtml( '<a href="' . $this->_baseurl . '/test/run/archive/?id=' . $test_run->id . '" class="ctmButton">Archive</a>' );
            }
            if ( $test_run->test_run_state_id == $queued_state->id || 
                 $test_run->test_run_state_id == $completed_state->id ||
                 $test_run->test_run_state_id == $archived_state->id ) {
               $this->printHtml( '<a href="' . $this->_baseurl . '/test/runs/?action=remove_test_run&test_run_id=' . $test_run->id . '" class="ctmButton">Remove</a>' );
            }

            $this->printHtml( '</center></td>' );
            $this->printHtml( '</tr>' );

            $sel = new CTM_Test_Run_Browser_Selector();
            $and_params = array(new Light_Database_Selector_Criteria('test_run_id', '=', $test_run->id));
            $test_run_browsers = $sel->find($and_params);

            if (count($test_run_browsers) > 0) {

                $this->printHtml('<tr>');
                    $this->printHtml('<td colspan="6">');
                        $this->printHtml('<b>Test Browser Runs<b>');
                    $this->printHtml('</td>');
                $this->printHtml('</tr>');

                foreach ($test_run_browsers as $test_run_browser) {

                    // failed?
                    if ($test_run_browser->test_run_state_id == 5) {
                        $testRunBrowserColor = '#FF0000';
                    } else {
                        $testRunBrowserColor = '#00FF00';
                    }

                    $this->printHtml('<tr>');
                        $this->printHtml('<td colspan="6" style="border-bottom: none;">');
                            $this->printHtml('<table style="width:100%;">');
                                $this->printHtml('<tr>');
                                    $this->printHtml('<td>' . $test_run_browser->id . '</td>');
                                    $this->printHtml('<td>' . $test_machine_cache->getById($test_run_browser->test_machine_id)->os . ' @ ' . $test_machine_cache->getById($test_run_browser->test_machine_id)->ip . '</td>');
                                    $this->printHtml('<td>' . $test_browser_cache->getById($test_run_browser->test_browser_id)->name . '</td>');
                                    $this->printHtml('<td style="background-color:' . $testRunBrowserColor . ';">' . $run_state_cache->getById($test_run_browser->test_run_state_id)->name . '</td>');
                                    $this->printHtml('<td><center><a href="' . $this->_baseurl . '/test/run/browser/log/?testRunBrowserId=' . $test_run_browser->id . '" class="ctmButton" target="_blank">Logs</a></center></td>');
                                $this->printHtml('</tr>');
                            $this->printHtml('</table>');
                        $this->printHtml('</td>');
                    $this->printHtml('</tr>');
                }

            }
            
         }
      }

      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="6"><center><a href="' . $this->_baseurl . '/test/run/add" class="ctmButton">Add Test Run</a></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_obj = new CTM_Site_Test_Runs();
$test_obj->displayPage();
