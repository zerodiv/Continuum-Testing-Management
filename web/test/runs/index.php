<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Cache.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Run/State/Cache.php' );

class CTM_Site_Test_Runs extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Runs';
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      return true;
   }

   public function displayBody() {
      $test_run_state_id = $this->getOrPost( 'test_run_state_id', 1 );

      $run_state_cache = new CTM_Test_Run_State_Cache();
      $user_cache = new CTM_User_Cache();

      $queued_state = $run_state_cache->getById( 1 );
      $executing_state = $run_state_cache->getById( 2 );
      $completed_state = $run_state_cache->getById( 3 );
      $archived_state = $run_state_cache->getById( 4 );

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
            $this->printHtml( '<td>' );
            $this->printHtml( '</td>' );
            $this->printHtml( '</tr>' );

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
