<?php 
require_once( '../../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Browser/Selector.php' );
require_once( 'CTM/Test/Machine/Browser/Selector.php' );
require_once( 'CTM/Test/Machine/Cache.php' );
require_once( 'CTM/Test/Run/Browser.php' );

class CTM_Site_Test_Run_Add_Step2 extends CTM_Site { 
   private $_test_machine_cache;

   public function setupPage() {
      $this->_test_machine_cache = new CTM_Test_Machine_Cache();

      $this->_pagetitle = 'Test Run - Add - Step 4 of 4';
      return true;
   }

   private function _availableMachinesForBrowser( $test_browser_id ) {
      $available_machines = array();

      try {
         $test_machine_browser_sel = new CTM_Test_Machine_Browser_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'test_browser_id', '=', $test_browser_id ),
         );
         $test_machine_browsers = $test_machine_browser_sel->find( $and_params );

         if ( count( $test_machine_browsers ) > 0 ) {
            foreach ( $test_machine_browsers as $test_machine_browser ) {
               // lookup the test_machine
               $test_machine = $this->_test_machine_cache->getById( $test_machine_browser->test_machine_id );
               if ( $test_machine->is_disabled == 0 ) {
                  $available_machines[] = $test_machine;
               }
            }
         }

      } catch ( Exception $e ) {
      }

      // randomize our return to seed the requests to a few machines.
      if ( count( $available_machines ) > 0 ) {
         shuffle( $available_machines );
      }

      return $available_machines;

   }

   public function handleRequest() {
      $id = $this->getOrPost( 'id', '' );
      $action = $this->getOrPost( 'action', '' );
      $test_browsers = $this->getOrPost( 'test_browsers', '' );

      $this->requiresAuth();

      if ( $action == 'step4' ) {
         // print_r( $test_browsers );

         // push the on ones as requests to specific machines by browser type.
         foreach ( $test_browsers as $test_browser_id => $on_off ) {
            // on_off is kinda a misnomer since some browsers don't transmit the off ones.
            // but since we're paranoid we will check anyways.
            if ( $on_off == 'on' ) {
               // find all machines with a browser_id that matches to this testing set.
               $test_machines = $this->_availableMachinesForBrowser( $test_browser_id );
               if ( count( $test_machines ) > 0 ) {
                  $test_machine = $test_machines[0];
                  try {
                     // inject the test_run -> machine relationship.
                     $test_run_browser_obj = new CTM_Test_Run_Browser();
                     $test_run_browser_obj->test_run_id = $id;
                     $test_run_browser_obj->test_browser_id = $test_browser_id;
                     $test_run_browser_obj->test_machine_id = $test_machine->id;
                     $test_run_browser_obj->test_run_state_id = 1;
                     $test_run_browser_obj->save();
                  } catch ( Exception $e ) {
                  }
               }
            }
         }

         header( 'Location: ' . $this->_baseurl . '/test/runs/' );

      }

      return true;

   }
                           

   public function displayBody() {
      $test_run_id = $this->getOrPost( 'id', '' );
      $test_run = null;
      $test_suite = null;
      $test_browsers = null;

      try {

         $sel = new CTM_Test_Run_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run_id ) );
         $test_runs = $sel->find( $and_params );
         
         if ( isset( $test_runs[0] ) ) {
            $test_run = $test_runs[0];
         }

         if ( isset( $test_run->id ) ) {
            $sel = new CTM_Test_Suite_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run->test_suite_id ) );
            $test_suites = $sel->find( $and_params );

            if ( isset( $test_suites[0] ) ) {
               $test_suite = $test_suites[0];
            }

            $floor_time = time() - (3600*3);

            $sel = new CTM_Test_Browser_Selector();
            $and_params = array( 
                  new Light_Database_Selector_Criteria( 'is_available', '=', 1 ),
                  new Light_Database_Selector_Criteria( 'last_seen', '>', $floor_time )
            );

            $test_browsers = $sel->find( $and_params, array(), array( 'name', 'major_version', 'minor_version', 'patch_version' ) );

         }
      } catch ( Exception $e ) {
      }

      if ( isset( $test_run->id ) ) {

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );

         $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/run/add/step4/">' );
         $this->printHtml( '<input type="hidden" name="action" value="step4">' );
         $this->printHtml( '<input type="hidden" name="id" value="' . $test_run->id .'">' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Add Test Run (Step 4 of 4)</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Test Suite:</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '</table>' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Test Browsers</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td>Test;</td>' );
         $this->printHtml( '<td>Browser:</td>' );
         $this->printHtml( '<td>Version:</td>' );
         $this->printHtml( '</tr>' );

         foreach ( $test_browsers as $test_browser ) {
            $class = $this->oddEvenClass();

            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td><center><input type="checkbox" name="test_browsers[' . $test_browser->id . ']" checked></center></td>' );
            $this->printHtml( '<td>' . $test_browser->name . '</td>' );
            $this->printHtml( '<td>' . 
                  $test_browser->major_version . '.' .
                  $test_browser->minor_version . '.' .
                  $test_browser->patch_version .
               '</td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="3"><center><input type="submit" value="Start Test"></center></td>' );
         $this->printHtml( '</tr>' ); 

         $this->printHtml( '</table>' );

         $this->printHtml( '</form>' );

         $this->printHtml( '</div>' );

      } 

      return true;

   }

}

$test_add_obj = new CTM_Site_Test_Run_Add_Step2();
$test_add_obj->displayPage();