<?php

require_once( 'Light/Config.php' );
require_once( 'CTM/Test/Run.php' );
require_once( 'CTM/Test/Cache.php' );
require_once( 'CTM/Test/Command/Selector.php' );
require_once( 'CTM/Test/Selenium/Command/Cache.php' );
require_once( 'CTM/Test/Run/BaseUrl.php' );
require_once( 'CTM/Test/Run/BaseUrl/Selector.php' );
require_once( 'CTM/Test/Run/BaseUrl/Cache.php' );
require_once( 'CTM/Test/Run/Command.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Suite/Cache.php' );
require_once( 'CTM/Test/Suite/Plan/Type/Cache.php' );
require_once( 'CTM/Test/Suite/Plan/Selector.php' );
require_once( 'CTM/Test/Param/Library/Cache.php' );

class CTM_Test_Run_Builder {
   private $_plan_type_cache;
   private $_test_suite_cache;
   private $_test_cache;
   private $_param_lib_cache;
   private $_test_run_baseurl_cache;
   private $_selenium_command_cache;

   private $_suite_name;
   private $_suite_dir;
   private $_suite_compressed_file;
   private $_suite_tests;
   private $_suite_test_id;

   function __construct() {

      $this->_plan_type_cache = new CTM_Test_Suite_Plan_Type_Cache();
      $this->_test_suite_cache = new CTM_Test_Suite_Cache();
      $this->_test_cache = new CTM_Test_Cache();
      $this->_param_lib_cache = new CTM_Test_Param_Library_Cache();
      $this->_test_run_baseurl_cache = new CTM_Test_Run_BaseUrl_Cache();
      $this->_selenium_command_cache = new CTM_Test_Selenium_Command_Cache();

      $this->_suite_name = null;
      $this->_suite_dir = null;
      $this->_suite_compressed_file = null;
      $this->_suite_tests = array();
      $this->_suite_test_id = 0;
   }

   public function build( CTM_Test_Run $test_run ) {

      try {

         // clear the existing run plan - if any.
         $this->_clearCurrentPlan( $test_run );
     
         // kick the build off with the parent suite
         $this->_addSuiteToPlan( $test_run, $test_run->test_suite_id );

         return true;

      } catch ( Exception $e ) {
         throw $e;
      }

   }

   public function buildTestSuite( CTM_Test_Run $test_run ) {
         try {
        
            // bring the suite in so we can get the name
            $test_suite = $this->_test_suite_cache->getById( $test_run->test_suite_id );

            $this->_suite_name = $test_suite->name;
            $this->_suite_dir = Light_Config::get('CTM_Config', 'SUITE_DIR' ) . '/' . $test_run->id;
            $this->_suite_compressed_file = $this->_suite_dir . '.zip';
            $this->_suite_tests = array();
            $this->_suite_test_id = 0;

            if ( is_dir( $this->_suite_dir ) ) {
               // cleanup the last try at building this out.
               system( 'rm -rf ' . $this->_suite_dir );
            }
            
            mkdir( $this->_suite_dir, 0755, true );

            if ( ! is_dir( $this->_suite_dir ) ) {
               return false;
            }

            if ( is_file( $this->_suite_compressed_file ) ) {
               unlink( $this->_suite_compressed_file );
            }

            // select the tests off the suite_plan stack.
            $this->_addTestSuiteToSuiteDir( $test_run, $test_run->test_suite_id );

            // write the final suite index.html
            $this->_writeSuiteHtml();

            // compress off the suite and make it easy for the agent to download.
            system( 'cd ' . Light_Config::get( 'CTM_Config', 'SUITE_DIR' ) . ' ; zip -q -r ' . $this->_suite_compressed_file . ' ' . $test_run->id . ' >/dev/null 2> /dev/null' );

         } catch ( Exception $e ) {
            throw $e;
         }

   }

   private function _addTestSuiteToSuiteDir( CTM_Test_Run $test_run, $test_suite_id ) {
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
                  $this->_addTestSuiteToSuiteDir( $test_run, $plan_step->linked_id );
               } else if ( $plan_type->name == 'test' ) {
                  $this->_addTestToSuiteDir( $test_run, $plan_step->linked_id );
               }

            }
         } 
      } catch ( Exception $e ) {
         throw $e;
      }

   }

   private function _addTestToSuiteDir( CTM_Test_Run $test_run, $test_id ) {

      try {
         // fetch the test_obj
         $test_obj = $this->_test_cache->getById( $test_id );

         // fetch all the test commands
         $sel = new CTM_Test_Command_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $test_id ) );
         $or_params = array();
         $order = array( 'id' );
         $test_commands = $sel->find( $and_params, $or_params, $order );

         // increment the _suite_test_id
         $this->_suite_test_id++;

         $this->_suite_tests[] = array(
               'suite_test_id' => $this->_suite_test_id,
               'test_obj' => $test_obj
         );

         $filename = $this->_suite_dir . '/' . $this->_suite_test_id . '.html';

         $fh = fopen( $filename, 'w' );

         if ( ! is_resource( $fh ) ) {
            return false;
         }

         // determine the baseurl for this test.
         $baseurl_obj = $this->_test_run_baseurl_cache->getByCompoundKey( $test_run->id, 0, $test_id );

         // eject the headers.
         fwrite( $fh, '<?xml version="1.0" encoding="UTF-8"?>' . "\n" );
         fwrite( $fh, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n" );
         fwrite( $fh, '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n" );
         fwrite( $fh, '<head profile="http://selenium-ide.openqa.org/profiles/test-case">' . "\n" );
         fwrite( $fh, '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n" );
         fwrite( $fh, '<link rel="selenium.base" href="' . $baseurl_obj->baseurl . '" />' . "\n" ); 
         fwrite( $fh, '<title>' . $this->_escapeVariable( $test_obj->name ) . '</title>' . "\n" );
         fwrite( $fh, '</head>' . "\n" );
         fwrite( $fh, '<body>' . "\n" );
         fwrite( $fh, '<table cellpadding="1" cellspacing="1" border="1">' . "\n" );
         fwrite( $fh, '<thead>' . "\n" );
         fwrite( $fh, '<tr><td rowspan="1" colspan="3">' . $this->_escapeVariable( $test_obj->name ) . '</td></tr>' . "\n" );
         fwrite( $fh, '</thead><tbody>' . "\n" );

         fwrite($fh, '<tr>' . "\n" );
         fwrite($fh, '         <td>open</td>' . "\n" );
         fwrite($fh, '         <td>' . $baseurl_obj->baseurl . '</td>' . "\n" );
         fwrite($fh, '         <td></td>' . "\n" );
         fwrite($fh, '</tr>' . "\n" );

         // dump all the test command combos to the file.
         if ( count( $test_commands ) > 0 ) {
            foreach ( $test_commands as $test_command ) {

               if ( $test_command->test_param_library_id > 0 ) {
                  // get the runtime override from the test_run_version.
                  // fetch the test run commands that need their values changed / adjusted.
                  $sel = new CTM_Test_Run_Command_Selector();
                  $and_params = array( 
                     new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ),
                     new Light_Database_Selector_Criteria( 'test_param_library_id', '=', $test_command->test_param_library_id )
                  );
                  $override_commands = $sel->find( $and_params );
                  if ( count( $override_commands ) > 0 ) {
                     $test_command = $override_commands[0];
                  }
               }
         
               $sel_obj = $this->_selenium_command_cache->getById( $test_command->test_selenium_command_id );
               $value_obj = $test_command->getValue();
               $target_obj = $test_command->getTarget();

               if ($sel_obj->name == 'open') {
                   $target = preg_replace('#/$#', '', $baseurl_obj->baseurl) . $target_obj->target;
               } else {
                   $target = $target_obj->target;
               }

               fwrite($fh, '<tr>' . "\n" );
               fwrite($fh, '         <td>' . $sel_obj->name . '</td>' . "\n" );
               fwrite($fh, '         <td>' . $target . '</td>' . "\n" );
               fwrite($fh, '         <td>' . $value_obj->value . '</td>' . "\n" );
               fwrite($fh, '</tr>' . "\n" );
               
            }
         }
         fwrite( $fh, '</tbody></table>' . "\n" );
         fwrite( $fh, '</body>' . "\n" );
         fwrite( $fh, '</html>' . "\n" );
         
         fclose( $fh );

      } catch ( Exception $e ) {
         throw $e;
      }
   }

   private function _escapeVariable( $var ) {
      $var = stripslashes( $var );
      return htmlentities( $var, ENT_QUOTES, 'UTF-8' );
   }

   private function _writeSuiteHtml() {

      $filename = $this->_suite_dir . '/index.html';

      $fh = fopen( $filename, 'w' );

      if ( ! is_resource( $fh ) ) {
         return false;
      }

      fwrite( $fh, '<html>' . "\n" );
      fwrite( $fh, '<head>' . "\n" );
      fwrite( $fh, '<title>' . $this->_escapeVariable( $this->_suite_name ) . '</title>' . "\n" );
      fwrite( $fh, '</head>' . "\n" );
      fwrite( $fh, '<body>' . "\n" );
      fwrite( $fh, '<table>' . "\n" );
      fwrite( $fh, '<tr><td><b>' . $this->_escapeVariable( $this->_suite_name ) . '</b></td></tr>' . "\n" );
      foreach ( $this->_suite_tests as $s_test ) {
         fwrite( $fh, 
               '<tr><td>' . 
               '<a href="./' . $s_test['suite_test_id'] . '.html">' . $this->_escapeVariable( $s_test['test_obj']->name ) . '</a>' .
               '</td></tr>' .
               "\n"
         );
      }
      fwrite( $fh, '</table>' . "\n" );
      fwrite( $fh, '</body>' . "\n" );
      fwrite( $fh, '</html>' . "\n" );

      fclose( $fh );

      return true;

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

         if ( is_dir( $this->_suite_dir ) ) {
            system( 'rm -rf ' . $this->_suite_dir );
         }

         if ( is_file( $this->_suite_compressed_file ) ) {
            unlink( $this->_suite_compressed_file );
         }

      } catch ( Exception $e ) {
         throw $e;
      }
      return true;
   }

   private function _addSuiteToPlan( CTM_Test_Run $test_run, $test_suite_id ) {
      // loop across the test_suite_plan and assemble the run.
      try {

         $test_suite = $this->_test_suite_cache->getById( $test_suite_id );

         $baseurl_sel = new CTM_Test_Run_BaseUrl_Selector();
         $baseurl_and_params = array( 
               new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ),
               new Light_Database_Selector_Criteria( 'test_suite_id', '=', $test_suite_id ),
               new Light_Database_Selector_Criteria( 'test_id', '=', 0 )
         );
         $run_baseurls = $baseurl_sel->find( $baseurl_and_params );

         if ( count($run_baseurls) == 0 ) {
            $suite_baseurl_obj = $test_suite->getBaseUrl();
            if ( is_object( $suite_baseurl_obj ) ) {
               $base_suite_obj = new CTM_Test_Run_BaseUrl();
               $base_suite_obj->test_run_id = $test_run->id;
               $base_suite_obj->test_suite_id = $test_suite_id;
               $base_suite_obj->test_id = 0;
               $base_suite_obj->baseurl = $suite_baseurl_obj->baseurl;
               $base_suite_obj->save();
            }
         }


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

         // increment the _suite_test_id
         $this->_suite_test_id++;

         $this->_suite_tests[] = array(
               'suite_test_id' => $this->_suite_test_id,
               'test_obj' => $test_obj
         );

         $test_obj = $this->_test_cache->getById( $test_id );

         if ( is_object( $test_obj ) ) {
            $baseurl_sel = new CTM_Test_Run_BaseUrl_Selector();
            $baseurl_and_params = array( 
               new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ),
               new Light_Database_Selector_Criteria( 'test_suite_id', '=', 0 ),
               new Light_Database_Selector_Criteria( 'test_id', '=', $test_id )
            );
            $run_baseurls = $baseurl_sel->find( $baseurl_and_params );
            if ( count( $run_baseurls ) == 0 ) {
               $test_baseurl_obj = $test_obj->getBaseUrl();
               if ( is_object( $test_baseurl_obj ) ) {
                  $base_suite_obj = new CTM_Test_Run_BaseUrl();
                  $base_suite_obj->test_run_id = $test_run->id;
                  $base_suite_obj->test_suite_id = 0;
                  $base_suite_obj->test_id = $test_id;
                  $base_suite_obj->baseurl = $test_baseurl_obj->baseurl;
                  $base_suite_obj->save();
               }
            }
         }

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
                  // copy only the parameter objects in.
                  if ( $test_command->test_param_library_id > 0 ) {
                     $test_run_command = new CTM_Test_Run_Command();
                     $test_run_command->test_run_id = $test_run->id;
                     $test_run_command->test_suite_id = $test_suite_id;
                     $test_run_command->test_id = $test_id;
                     $test_run_command->test_selenium_command_id = $test_command->test_selenium_command_id;
                     $test_run_command->test_param_library_id = $test_command->test_param_library_id;
                     $test_run_command->save();

                     // pull the test library item out-
                     $param_lib_obj = $this->_param_lib_cache->getById( $test_run_command->test_param_library_id );
                     $default_obj = $param_lib_obj->getDefault();
                     $test_run_command->setTarget( $default_obj->default_value );
                     $test_run_command->setValue( $param_lib_obj->name );
                  }

               }

            }
         }

      } catch ( Exception $e ) {
         throw $e;
      }
   }

}
