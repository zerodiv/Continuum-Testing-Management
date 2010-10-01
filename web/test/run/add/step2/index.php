<?php

require_once( '../../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );

class CTM_Site_Test_Run_Add_Step2 extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Run - Add - Step 2 of 4');
      return true;
   }

   public function handleRequest() {
      $id = $this->getOrPost( 'id', '' );
      $action = $this->getOrPost( 'action', '' );
      $test_params = $this->getOrPost( 'test_params', array() );

      $this->requiresAuth();

      if ( $action != 'step2' ) {
         return true;
      }
      
      if ( count( $test_params ) == 0 ) {
         header( 'Location: ' . $this->getBaseUrl() . '/test/run/add/step3/?id=' . $id );
         return false;
      }

      try {
         $test_run = null;
         $sel = new CTM_Test_Run_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );
         $test_runs = $sel->find( $and_params );
         
         if ( isset( $test_runs[0] ) ) {
            $test_run = $test_runs[0];

            $sel = new CTM_Test_Run_Command_Selector();
            foreach ( $test_params as $test_param_id => $test_param_value ) {
               $and_params = array( 
                  new Light_Database_Selector_Criteria( 'id', '=', $test_param_id ),
               );
               $t_parms = $sel->find( $and_params );
               if ( isset( $t_parms[0] ) ) {
                  $t_parm = $t_parms[0];
                  $t_parm->setTarget( $test_param_value );
               }
            }
            header( 'Location: ' . $this->getBaseUrl() . '/test/run/add/step3/?id=' . $id );
            return false;
         }
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

         $test_run_state_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Run_State_Cache' );
         $step2 = $test_run_state_cache->getByName('step2');

         $test_run->test_run_state_id = $step2->id;
         $test_run->save();

         $sel_command_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Selenium_Command_Cache' );

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );

         $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/test/run/add/step2/">' );
         $this->printHtml( '<input type="hidden" name="action" value="step2">' );
         $this->printHtml( '<input type="hidden" name="id" value="' . $test_run->id .'">' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="2">Add Test Run (Step 2 of 4)</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Test Suite:</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
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
               $this->printHtml( '<td><input type="text" size="40" value="' . $this->escapeVariable( $target_obj->target ) . '" name="test_params[' . $test_parm->id . ']"></td>' );
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
