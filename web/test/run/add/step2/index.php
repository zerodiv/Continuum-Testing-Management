<?php

require_once( '../../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );

class CTM_Site_Test_Run_Add_Step2 extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Run - Add - Step 2 of 2';
      return true;
   }

   public function handleRequest() {
      $test_suite_id = $this->getOrPost( 'test_suite_id', '' );
      $test_run_id = $this->getOrPost( 'test_run_id', '' );
      $iterations = $this->getOrPost( 'iterations', '' );

      $this->requiresAuth();

      try {
      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $test_run_id = $this->getOrPost( 'id', '' );

      $test_run = null;
      $test_suite = null;
      $test_parms = null;

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

            // fetch the test run commands that need their values changed / adjusted.
            $sel = new CTM_Test_Run_Command_Selector();
            $and_params = array( 
                  new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ),
                  new Light_Database_Selector_Criteria( 'test_param_library_id', '!=', 0 )
            );
            $test_parms = $sel->find( $and_params );
         }
      } catch ( Exception $e ) {
      }

      if ( isset( $test_run->id ) ) {
        
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="2">Add Test Run</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Test Suite:</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Iterations:</td>' );
         $this->printHtml( '<td><input type="text" name="iterations" size="3" value="1"></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '</table>' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th>Test Parameters</th>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td>Value:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '</table>' );

         $this->printHtml( '</div>' );

      } 

      return true;

   }

}

$test_add_obj = new CTM_Site_Test_Run_Add_Step2();
$test_add_obj->displayPage();
