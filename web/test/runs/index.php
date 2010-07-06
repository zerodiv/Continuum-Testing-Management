<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run/Selector.php' );

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
      $run_state_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Run_State_Cache' );
      $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );
      $test_machine_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Machine_Cache' );
      $test_browser_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Browser_Cache' );

      $queued_state = $run_state_cache->getById( 1 );
      $executing_state = $run_state_cache->getById( 2 );
      $completed_state = $run_state_cache->getById( 3 );
      $archived_state = $run_state_cache->getById( 4 );
      $failed_state = $run_state_cache->getById(5);

      if ( ! isset( $run_state->id ) ) {
         // load up the queued page if we cannot find the current run state.
         $run_state = $run_state_cache->getById( 1 );
      }

      $test_runs = null;
      try {
         $sel = new CTM_Test_Run_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_run_state_id', '!=', $archived_state->id ) );
         $test_runs = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="7">Test Runs</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="7"><center><a href="' . $this->_baseurl . '/test/run/add" class="ctmButton">Add Test Run</a></center></td>' );
      $this->printHtml( '</tr>' );

      if ( count( $test_runs ) == 0 ) {
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="7"><center> - There are no test runs in a non archived state.</center></td>' );
         $this->printHtml( '</tr>' );
      } else {
         foreach ( $test_runs as $test_run ) {
      
            $this->printHtml( '<tr class="aiTableTitle">' );
            $this->printHtml( '<td class="aiColumnOne">ID</td>' );
            $this->printHtml( '<td>Test Suite</td>' );
            $this->printHtml( '<td>Status</td>' );
            $this->printHtml( '<td>Iterations</td>' );
            $this->printHtml( '<td>Created At</td>' );
            $this->printHtml( '<td>Created By</td>' );
            $this->printHtml( '<td>Actions</td>' );
            $this->printHtml( '</tr>' );

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
            
            if ($test_run->test_run_state_id == 5) {
               $testRunColor = '#FF0000';
            } else {
               $testRunColor = '#00FF00';
            }


            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td class="aiColumnOne">' . $test_run->id . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
            $this->printHtml('<td style="background-color:' . $testRunColor . ';"><center>' . $run_state_cache->getById($test_run->test_run_state_id)->name . '</center></td>');
            $this->printHtml( '<td>' . $test_run->iterations . '</td>' );
            $this->printHtml( '<td>' . $this->formatDate( $test_run->created_at ) . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $created_by->username ) . '</td>' );
            $this->printHtml( '<td><center>' );
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/run/download/?id=' . $test_run->id . '" class="ctmButton">Download</a>' );
            // while a test is executing we cannot do any admin actions to it.
            if ( $test_run->test_run_state_id == $queued_state->id || 
                 $test_run->test_run_state_id == $completed_state->id ||
                 $test_run->test_run_state_id == $failed_state->id ||
                 $test_run->test_run_state_id == $archived_state->id ) {
               $this->printHtml( '<a href="' . $this->_baseurl . '/test/runs/?action=remove_test_run&test_run_id=' . $test_run->id . '" class="ctmButton">Remove</a>' );
            }
            $this->printHtml( '</center></td>' );
            $this->printHtml( '</tr>' );

            $sel = new CTM_Test_Run_Browser_Selector();
            $and_params = array(new Light_Database_Selector_Criteria('test_run_id', '=', $test_run->id));
            $test_run_browsers = $sel->find($and_params);

            if (count($test_run_browsers) > 0) {

                $this->printHtml('<tr><td valign="top" colspan="7">' );
                $this->printHtml( '<table class="ctmTable aiFullWidth">' );

                $this->printHtml('<tr class="aiTableTitle">');
                $this->printHtml('<td colspan="6">Test Browser Runs for: ' . $this->escapeVariable( $test_suite->name ) . '</td>');
                $this->printHtml('</tr>');

                $this->printHtml('<tr class="aiTableTitle">' );
                $this->printHtml('<td>ID</td>' );
                $this->printHtml('<td>OS</td>' );
                $this->printHtml('<td>Browser</td>' );
                $this->printHtml('<td>Status</td>' );
                $this->printHtml('<td>Actions</td>' );
                $this->printHtml('</tr>' );

                foreach ($test_run_browsers as $test_run_browser) {

                    // failed?
                    if ($test_run_browser->test_run_state_id == 5) {
                        $testRunBrowserColor = '#FF0000';
                    } else {
                        $testRunBrowserColor = '#00FF00';
                    }

                    $test_machine = $test_machine_cache->getById( $test_run_browser->test_machine_id );
                    $t_machine = $test_machine->ip;
                    if ( $test_machine->machine_name != '' ) {
                        $t_machine = $test_machine->machine_name;
                    }

                    $test_browser = $test_browser_cache->getById( $test_run_browser->test_browser_id );

                    $this->printHtml('<tr class="' . $this->oddEvenClass() . '">');
                    $this->printHtml('<td>' . $test_run_browser->id . '</td>');
                    $this->printHtml('<td>' . $test_machine->os . ' @ ' . $t_machine . '</td>');
                    $this->printHtml('<td>' . $test_browser->getPrettyName() . '</td>');
                    $this->printHtml('<td style="background-color:' . $testRunBrowserColor . ';"><center>' . $run_state_cache->getById($test_run_browser->test_run_state_id)->name . '</center></td>');
                    if ( $test_run_browser->has_log == true ) {
                        $this->printHtml('<td><center>' .
                              '<a href="' . $this->_baseurl . '/test/run/browser/log/?testRunBrowserId=' . $test_run_browser->id . '&type=run" class="ctmButton" target="_blank">Run Log</a>' .
                              '<a href="' . $this->_baseurl . '/test/run/browser/log/?testRunBrowserId=' . $test_run_browser->id . '&type=selenium" class="ctmButton" target="_blank">Selenium Log</a>' .

                              '</center></td>');
                    } else {
                       $this->printHtml( '<td>&nbsp;</td>' );
                    }
                    $this->printHtml('</tr>');
                }

                $this->printHtml('</table>');
                $this->printHtml('</td>' );
                $this->printHtml('</tr>' );

            }
            
         }
      }

      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="7"><center><a href="' . $this->_baseurl . '/test/run/add" class="ctmButton">Add Test Run</a></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_obj = new CTM_Site_Test_Runs();
$test_obj->displayPage();
