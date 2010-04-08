<?php

require_once( '../../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Selenium/Command/Cache.php' );

class CTM_Site_Test_Run_Add_Step2 extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Run - Add - Step 3 of 3';
      return true;
   }

   public function handleRequest() {
      $test_suite_id = $this->getOrPost( 'test_suite_id', '' );
      $test_run_id = $this->getOrPost( 'test_run_id', '' );
      $iterations = $this->getOrPost( 'iterations', '' );

      $this->requiresAuth();

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

         $sel_command_cache = new CTM_Test_Selenium_Command_Cache();

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );

         $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/run/add/step3/">' );
         $this->printHtml( '<input type="hidden" name="action" value="step2">' );
         $this->printHtml( '<input type="hidden" name="id" value="' . $test_run->id .'">' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="2">Add Test Run (Step 2 of 3)</th>' );
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
         $this->printHtml( '<th colspan="2">Test Parameters</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td>Param Name:</td>' );
         $this->printHtml( '<td>Value:</td>' );
         $this->printHtml( '</tr>' );

         if ( count( $test_parms ) > 0 ) {
            foreach ( $test_parms as $test_parm ) {
               
               $class = $this->oddEvenClass();

               // $sel_comm = $sel_command_cache->getById( $test_parm->test_selenium_command_id );

               $target_obj = $test_parm->getTarget();

               $value_obj = $test_parm->getValue();

               $this->printHtml( '<tr class="' . $class . '">' );
               $this->printHtml( '<td>' . $this->escapeVariable( $value_obj->value ) . '</td>' );
               $this->printHtml( '<td><input type="text" size="40" value="' . $this->escapeVariable( $target_obj->target ) . '" name="target[' . $test_parm->id . ']"></td>' );
               $this->printHtml( '</tr>' );

            }
         } else {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="2"><center>- No test parameters to configure -</center></td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="2"><center><input type="submit" value="Next: Configure Urls"></center></td>' );
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
