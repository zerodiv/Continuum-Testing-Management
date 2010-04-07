<?php

require_once( 'CTM/Test/Run.php' );

require_once( 'CTM/Test/Command/Selector.php' );
require_once( 'CTM/Test/Run/Command.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Suite/Plan/Type/Cache.php' );
require_once( 'CTM/Test/Suite/Plan/Selector.php' );
require_once( 'CTM/Test/Param/Library/Cache.php' );

class CTM_Test_Run_Builder {
   
   function __construct() {
      $this->_plan_type_cache = new CTM_Test_Suite_Plan_Type_Cache();
   }

   public function build( CTM_Test_Run $test_run ) {

      try {
         // clear the existing run plan - if any.
         $this->_clearCurrentPlan( $test_run );
     
         // kick the build off with the parent suite
         $this->_addSuiteToPlan( $test_run, $test_run->test_suite_id );

      } catch ( Exception $e ) {
      }


   }

   private function _clearCurrentPlan( CTM_Test_Run $test_run ) {
      try {
         $sel = new CTM_Test_Run_Command_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ) );
         $existing_commands = $sel->find( $and_params );

         if ( count( $existing_commands ) > 0 ) {
            foreach ( $existing_commands as $existing_command ) {
               $existing_command->remove();
            }
         }

      } catch ( Exception $e ) {
         throw $e;
      }
      return true;
   }

   private function _addSuiteToPlan( CTM_Test_Run $test_run, $test_suite_id ) {
      // loop across the test_suite_plan and assemble the run.
      try {

         $sel = new CTM_Test_Suite_Plan_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_suite_id', '=', $test_suite_id ) );
         $plan_steps = $sel->find( $and_params );

         if ( count( $plan_steps ) > 0 ) {
            foreach ( $plan_steps as $plan_step ) {
               // is the step a suite or a test?
               $plan_type = $this->_plan_type_cache->getById( $plan_step->test_suite_plan_type_id );

               // we have to exclude linking back to the parent for the run.. this prevents infinite
               // loops.
               if ( $plan_type->name == 'suite' && $plan_step->linked_id != $test_run->test_suite_id ) {
                  $this->_addSuiteToPlan( $test_run, $plan_step->linked_id );
               }

               if ( $plan_type->name == 'test' ) {
                  $this->_addTestToPlan( $test_run, $test_suite_id, $plan_step->linked_id );
               }

            }
         } 

      } catch ( Exception $e ) {
         throw $e;
      }
      return true;
   }

   private function _addTestToPlan( CTM_Test_Run $test_run, $test_suite_id, $test_id ) {
      try {
         $param_lib_cache = new CTM_Test_Param_Library_Cache();

         $sel = new CTM_Test_Command_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $test_id ) );
         $or_params = array();
         $order = array( 'id' );
         $test_commands = $sel->find( $and_params, $or_params, $order );

         if ( count( $test_commands ) > 0 ) {
            foreach ( $test_commands as $test_command ) {
               $add_to_stack = false;
               if ( $test_command->test_param_library_id > 0 ) {
                  // only allow one copy of the param in the test set.
                  $param_sel = new CTM_Test_Run_Command_Selector();
                  $param_and_params = array( 
                        new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ),
                        new Light_Database_Selector_Criteria( 'test_param_library_id', '=', $test_command->test_param_library_id ),
                  );
                  $p_params = $param_sel->find( $param_and_params );
                  if ( count( $p_params ) > 0 ) {
                     $add_to_stack = false;
                  } else {
                     $add_to_stack = true;
                  }
               } else {
                  // inject the command into the run.
                  $add_to_stack = true;
               }

               if ( $add_to_stack == true ) {
                  // copy the object in.
                  $test_run_command = new CTM_Test_Run_Command();
                  $test_run_command->test_run_id = $test_run->id;
                  $test_run_command->test_suite_id = $test_suite_id;
                  $test_run_command->test_id = $test_id;
                  $test_run_command->test_selenium_command_id = $test_command->test_selenium_command_id;
                  $test_run_command->test_param_library_id = $test_command->test_param_library_id;
                  $test_run_command->save();

                  // copy the text blobs over.
                  if ( $test_run_command->test_param_library_id > 0 ) {
                     // pull the test library item out-
                     $param_lib_obj = $param_lib_cache->getById( $test_run_command->test_param_library_id );
                     $default_obj = $param_lib_obj->getDefault();
                     $test_run_command->setTarget( $default_obj->default_value );
                     $test_run_command->setValue( $param_lib_obj->name );
                  } else {
                     $target_obj = $test_command->getTarget();
                     $value_obj = $test_command->getValue();

                     $test_run_command->setTarget( $target_obj->target );
                     $test_run_command->setValue( $value_obj->value );
                  }

               }

            }
         }

      } catch ( Exception $e ) {
         throw $e;
      }
   }

}
