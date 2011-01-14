<?php

require_once( 'Light/Config.php');
require_once( 'Light/Database/Object/Cache/Factory.php');

require_once( 'CTM/Test/Param.php');
require_once( 'CTM/Test/Param/Selector.php');
require_once( 'CTM/Test/Run.php');
require_once( 'CTM/Test/Command/Selector.php');
require_once( 'CTM/Test/Run/BaseUrl.php');
require_once( 'CTM/Test/Run/BaseUrl/Selector.php');
require_once( 'CTM/Test/Run/Command.php');
require_once( 'CTM/Test/Run/Command/Selector.php');
require_once( 'CTM/Test/Suite/Plan/Selector.php');

class CTM_Test_Run_Builder
{
   private $_planTypeCache;
   private $_testSuiteCache;
   private $_testCache;
   private $_paramLibCache;
   private $_testRunBaseurlCache;
   private $_seleniumCommandCache;

   private $_suiteName;
   private $_suiteDir;
   private $_suiteCompressedFile;
   private $_suiteTests;
   private $_suiteTestId;

   function __construct()
   {

      $this->_planTypeCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Suite_Plan_Type_Cache');
      $this->_testSuiteCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Suite_Cache');
      $this->_testCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Cache');
      $this->_paramLibCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Param_Library_Cache');
      $this->_testRunBaseurlCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Run_BaseUrl_Cache');
      $this->_seleniumCommandCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Selenium_Command_Cache');

      $this->_suiteName = null;
      $this->_suiteDir = null;
      $this->_suiteCompressedFile = null;
      $this->_suiteTests = array();
      $this->_suiteTestId = 0;
   }

   public function build( CTM_Test_Run $testRun )
   {

      try {

         // clear the existing run plan - if any.
         $this->_clearCurrentPlan($testRun);
     
         // kick the build off with the parent suite
         $this->_addSuiteToPlan($testRun, $testRun->testSuiteId);

         return true;

      } catch ( Exception $e ) {
         throw $e;
      }

   }

   public function buildTestSuite( CTM_Test_Run $testRun )
   {
         try {
        
            // bring the suite in so we can get the name
            $testSuite = $this->_testSuiteCache->getById($testRun->testSuiteId);

            $this->_suiteName = $testSuite->name;
            $this->_suiteDir = Light_Config::get('CTM_Config', 'SUITE_DIR') . '/' . $testRun->id;
            $this->_suiteCompressedFile = $this->_suiteDir . '.zip';
            $this->_suiteTests = array();
            $this->_suiteTestId = 0;

            if ( is_dir($this->_suiteDir) ) {
               // cleanup the last try at building this out.
               system('rm -rf ' . $this->_suiteDir);
            }
            
            mkdir($this->_suiteDir, 0755, true);

            if ( ! is_dir($this->_suiteDir) ) {
               return false;
            }

            if ( is_file($this->_suiteCompressedFile) ) {
               unlink($this->_suiteCompressedFile);
            }

            // select the tests off the suite_plan stack.
            $this->_addTestSuiteToSuiteDir($testRun, $testRun->testSuiteId);

            // write the final suite index.html
            $this->_writeSuiteHtml();

            // compress off the suite and make it easy for the agent to download.
            system(
                'cd ' . Light_Config::get('CTM_Config', 'SUITE_DIR') . ' ; ' .
                'zip -q -r ' . $this->_suiteCompressedFile . ' ' . $testRun->id . 
                ' >/dev/null 2> /dev/null'
            );

         } catch ( Exception $e ) {
            throw $e;
         }

   }

   private function _addTestSuiteToSuiteDir( CTM_Test_Run $testRun, $testSuiteId )
   {
      try {

         $sel = new CTM_Test_Suite_Plan_Selector();
         $andParams = array( new Light_Database_Selector_Criteria('testSuiteId', '=', $testSuiteId ));
         $planSteps = $sel->find($andParams); 
         
         if ( count($planSteps) > 0 ) {
            foreach ( $planSteps as $planStep ) {
               // is the step a suite or a test?
               $planType = $this->_planTypeCache->getById($planStep->testSuitePlanTypeId); 
               
               // we have to exclude linking back to the parent for the run.. this prevents infinite
               // loops.
               if ( $planType->name == 'suite' && $planStep->linkedId != $testRun->testSuiteId ) {
                  $this->_addTestSuiteToSuiteDir($testRun, $planStep->linkedId);
               } else if ( $planType->name == 'test' ) {
                  $this->_addTestToSuiteDir($testRun, $planStep->linkedId);
               }

            }
         } 
      } catch ( Exception $e ) {
         throw $e;
      }

   }

   private function _addTestToSuiteDir( CTM_Test_Run $testRun, $testId )
   {

      try {
         // fetch the testObj
         $testObj = $this->_testCache->getById($testId);

         // fetch all the test commands
         $sel = new CTM_Test_Command_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testId', '=', $testId ));
         $orParams = array();
         $order = array( 'id');
         $testCommands = $sel->find($andParams, $orParams, $order);

         // increment the _suiteTestId
         $this->_suiteTestId++;

         $this->_suiteTests[] = array(
               'suiteTestId' => $this->_suiteTestId,
               'testObj' => $testObj
         );

         $filename = $this->_suiteDir . '/' . $this->_suiteTestId . '.html';

         $fh = fopen($filename, 'w');

         if ( ! is_resource($fh) ) {
            return false;
         }

         // determine the baseurl for this test.
         $baseurlObj = $this->_testRunBaseurlCache->getByCompoundKey($testRun->id, 0, $testId);

         // eject the headers.
         fwrite($fh, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
         fwrite(
             $fh,
             '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" ' .
             '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . 
             "\n"
         );
         fwrite($fh, '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n");
         fwrite($fh, '<head profile="http://selenium-ide.openqa.org/profiles/test-case">' . "\n");
        
         // jeo - temporarily removing this piece.
         // fwrite($fh, '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n");

         fwrite($fh, '<link rel="selenium.base" href="' . $baseurlObj->cleanBaseUrl() . '" />' . "\n"); 
         fwrite($fh, '<title>' . $this->_escapeVariable($testObj->name) . '</title>' . "\n");
         fwrite($fh, '</head>' . "\n");
         fwrite($fh, '<body>' . "\n");
         fwrite($fh, '<table cellpadding="1" cellspacing="1" border="1">' . "\n");
         fwrite($fh, '<thead>' . "\n");
         fwrite(
             $fh,
             '<tr><td rowspan="1" colspan="3">' . 
             $this->_escapeVariable($testObj->name) . 
             '</td></tr>' . 
             "\n"
         );
         fwrite($fh, '</thead><tbody>' . "\n");

         fwrite($fh, '<tr>' . "\n");
         fwrite($fh, '         <td>open</td>' . "\n");
         fwrite($fh, '         <td>' . $baseurlObj->baseurl . '</td>' . "\n");
         fwrite($fh, '         <td></td>' . "\n");
         fwrite($fh, '</tr>' . "\n");

         // pull in all the testParamLibraryId values
         $sel = new CTM_Test_Run_Command_Selector();
         $andParams = array( 
               new Light_Database_Selector_Criteria( 'testRunId', '=', $testRun->id ),
               new Light_Database_Selector_Criteria( 'testParamLibraryId', '!=', 0 )
         );
         $testParams = $sel->find($andParams);
         if ( count($testParams) > 0 ) {
            foreach ( $testParams as $testCommand ) {
               $selObj = $this->_seleniumCommandCache->getById($testCommand->testSeleniumCommandId);
               $valueObj = $testCommand->getValue();
               $targetObj = $testCommand->getTarget();
               fwrite($fh, '<tr>' . "\n");
               fwrite($fh, '         <td>' . $selObj->name . '</td>' . "\n");
               fwrite($fh, '         <td>' . $targetObj->target . '</td>' . "\n");
               fwrite($fh, '         <td>' . $valueObj->value . '</td>' . "\n");
               fwrite($fh, '</tr>' . "\n");
            }
         }

         // dump all the test command combos to the file.
         if ( count($testCommands) > 0 ) {

            foreach ( $testCommands as $testCommand ) {

               if ( $testCommand->testParamLibraryId > 0 ) {
                  // get the runtime override from the testRun_version.
                  // fetch the test run commands that need their values changed / adjusted.
                  $sel = new CTM_Test_Run_Command_Selector();
                  $andParams = array( 
                     new Light_Database_Selector_Criteria( 'testRunId', '=', $testRun->id ),
                     new Light_Database_Selector_Criteria( 'testParamLibraryId', '=', $testCommand->testParamLibraryId )
                  );
                  $overrideCommands = $sel->find($andParams);
                  if ( count($overrideCommands) > 0 ) {
                     $testCommand = $overrideCommands[0];
                  }
               }
         
               $selObj = $this->_seleniumCommandCache->getById($testCommand->testSeleniumCommandId);
               $valueObj = $testCommand->getValue();
               $targetObj = $testCommand->getTarget();

               /*
               JEO - This adds the base url to the open call.. we shouldn't be needing this anymore
               if ($selObj->name == 'open') {
                   $target = preg_replace('#/$#', '', $baseurlObj->baseurl) . $target_obj->target;
               } else {
                   $target = $target_obj->target;
               }
               */

               if ( $selObj->name == '#comment#' ) {
                  fwrite($fh, '<!-- ' . $this->_escapeVariable($targetObj->target) . ' -->' . "\n");
               } else {
                  fwrite($fh, '<tr>' . "\n");
                  fwrite($fh, '         <td>' . $selObj->name . '</td>' . "\n");
                  fwrite($fh, '         <td>' . $targetObj->target . '</td>' . "\n");
                  fwrite($fh, '         <td>' . $valueObj->value . '</td>' . "\n");
                  fwrite($fh, '</tr>' . "\n");
               }

               
            }
         }
         fwrite($fh, '</tbody></table>' . "\n");
         fwrite($fh, '</body>' . "\n");
         fwrite($fh, '</html>' . "\n");
         
         fclose($fh);

      } catch ( Exception $e ) {
         throw $e;
      }
   }

   private function _escapeVariable( $var )
   {
      $var = stripslashes($var);
      return htmlentities($var, ENT_QUOTES, 'UTF-8');
   }

   private function _writeSuiteHtml()
   {

      $filename = $this->_suiteDir . '/index.html';

      $fh = fopen($filename, 'w');

      if ( ! is_resource($fh) ) {
         return false;
      }

      fwrite($fh, '<html>' . "\n");
      fwrite($fh, '<head>' . "\n");
      fwrite($fh, '<title>' . $this->_escapeVariable($this->_suiteName) . '</title>' . "\n");
      fwrite($fh, '</head>' . "\n");
      fwrite($fh, '<body>' . "\n");
      fwrite($fh, '<table>' . "\n");
      fwrite($fh, '<tr><td><b>' . $this->_escapeVariable($this->_suiteName) . '</b></td></tr>' . "\n");
      foreach ( $this->_suiteTests as $sTest ) {
         fwrite(
             $fh, 
             '<tr><td>' . 
             '<a href="./' . $sTest['suiteTestId'] . '.html">' . 
             $this->_escapeVariable($sTest['testObj']->name) . 
             '</a>' .
             '</td></tr>' .
             "\n"
         );
      }
      fwrite($fh, '</table>' . "\n");
      fwrite($fh, '</body>' . "\n");
      fwrite($fh, '</html>' . "\n");

      fclose($fh);

      return true;

   }

   private function _clearCurrentPlan( CTM_Test_Run $testRun )
   {
      try {
         $sel = new CTM_Test_Run_Command_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testRunId', '=', $testRun->id ));
         $existingCommands = $sel->find($andParams);

         if ( count($existingCommands) > 0 ) {
            foreach ( $existingCommands as $existingCommand ) {
               $existingCommand->remove();
            }
         }

         if ( is_dir($this->_suiteDir) ) {
            system('rm -rf ' . $this->_suiteDir);
         }

         if ( is_file($this->_suiteCompressedFile) ) {
            unlink($this->_suiteCompressedFile);
         }

      } catch ( Exception $e ) {
         throw $e;
      }
      return true;
   }

   private function _addSuiteToPlan( CTM_Test_Run $testRun, $testSuiteId )
   {
      // loop across the test_suite_plan and assemble the run.
      try {

         $testSuite = $this->_testSuiteCache->getById($testSuiteId);

         $baseurlSel = new CTM_Test_Run_BaseUrl_Selector();
         $baseurlAndParams = array( 
               new Light_Database_Selector_Criteria( 'testRunId', '=', $testRun->id ),
               new Light_Database_Selector_Criteria( 'testSuiteId', '=', $testSuiteId ),
               new Light_Database_Selector_Criteria( 'testId', '=', 0 )
         );
         $runBaseurls = $baseurlSel->find($baseurlAndParams);

         $sel = new CTM_Test_Suite_Plan_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testSuiteId', '=', $testSuiteId ));
         $planSteps = $sel->find($andParams);

         if ( count($planSteps) > 0 ) {
            foreach ( $planSteps as $planStep ) {
               // is the step a suite or a test?
               $planType = $this->_planTypeCache->getById($planStep->testSuitePlanTypeId);

               // we have to exclude linking back to the parent for the run.. this prevents infinite
               // loops.
               if ( $planType->name == 'suite' && $planStep->linkedId != $testRun->testSuiteId ) {
                  $this->_addSuiteToPlan($testRun, $planStep->linkedId);
               }

               if ( $planType->name == 'test' ) {
                  $this->_addTestToPlan($testRun, $testSuiteId, $planStep->linkedId);
               }

            }
         } 

      } catch ( Exception $e ) {
         throw $e;
      }
      return true;
   }

   private function _addTestToPlan( CTM_Test_Run $testRun, $testSuiteId, $testId )
   {
      try {

         // increment the _suiteTestId
         $this->_suiteTestId++;

         $testObj = $this->_testCache->getById($testId);

         $this->_suiteTests[] = array(
               'suiteTestId' => $this->_suiteTestId,
               'testObj' => $testObj
         );

         if ( is_object($testObj) ) {
         

            $baseurlSel = new CTM_Test_Run_BaseUrl_Selector();
            $baseurlAndParams = array( 
               new Light_Database_Selector_Criteria( 'testRunId', '=', $testRun->id ),
               new Light_Database_Selector_Criteria( 'testSuiteId', '=', 0 ),
               new Light_Database_Selector_Criteria( 'testId', '=', $testId )
            );
            $runBaseurls = $baseurlSel->find($baseurlAndParams);
            if ( count($runBaseurls) == 0 ) {
               $testBaseurlObj = $testObj->getBaseUrl();
               if ( is_object($testBaseurlObj) ) {
                  $baseSuiteObj = new CTM_Test_Run_BaseUrl();
                  $baseSuiteObj->testRunId = $testRun->id;
                  $baseSuiteObj->testSuiteId = 0;
                  $baseSuiteObj->testId = $testId;
                  $baseSuiteObj->baseurl = $testBaseurlObj->baseurl;
                  $baseSuiteObj->save();
               }
            }
         }

         // pull in all the ctm test parameters that this test needs first.
         $sel = new CTM_Test_Param_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testId', '=', $testId ));
         $testParams = $sel->find($andParams);

         if ( count($testParams) > 0 ) {
            foreach ( $testParams as $testParam ) {
               $paramLibObj = $this->_paramLibCache->getById($testParam->testParamLibraryId);

               $testRunCommand = new CTM_Test_Run_Command();
               $testRunCommand->testRunId = $testRun->id;
               $testRunCommand->testSuiteId = $testSuiteId;
               $testRunCommand->testId = $testId;
               $testRunCommand->testSeleniumCommandId = 1; // store.
               $testRunCommand->testParamLibraryId = $testParam->testParamLibraryId;
               $testRunCommand->save();

               $defaultObj = $paramLibObj->getDefault();
               $testRunCommand->setTarget($defaultObj->defaultValue);
               $testRunCommand->setValue($paramLibObj->name);

            }
         }

         // now loop across the normal commands for the 
         $sel = new CTM_Test_Command_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testId', '=', $testId ));
         $orParams = array();
         $order = array( 'id');
         $testCommands = $sel->find($andParams, $orParams, $order);

         if ( count($testCommands) > 0 ) {
            foreach ( $testCommands as $testCommand ) {
               $addToStack = false;
               if ( $testCommand->testParamLibraryId > 0 ) {
                  // only allow one copy of the param in the test set.
                  $paramSel = new CTM_Test_Run_Command_Selector();
                  $paramAndParams = array( 
                        new Light_Database_Selector_Criteria( 'testRunId', '=', $testRun->id ),
                        new Light_Database_Selector_Criteria( 
                           'testParamLibraryId', '=', $testCommand->testParamLibraryId
                        )
                  );
                  $pParams = $paramSel->find($paramAndParams);
                  if ( count($pParams) > 0 ) {
                     $addToStack = false;
                  } else {
                     $addToStack = true;
                  }
               } else {
                  // inject the command into the run.
                  $addToStack = true;
               }

               if ( $addToStack == true ) {
                  // copy only the parameter objects in.
                  if ( $testCommand->testParamLibraryId > 0 ) {
                     $testRunCommand = new CTM_Test_Run_Command();
                     $testRunCommand->testRunId = $testRun->id;
                     $testRunCommand->testSuiteId = $testSuiteId;
                     $testRunCommand->testId = $testId;
                     $testRunCommand->testSeleniumCommandId = $testCommand->testSeleniumCommandId;
                     $testRunCommand->testParamLibraryId = $testCommand->testParamLibraryId;
                     $testRunCommand->save();

                     // pull the test library item out-
                     $paramLibObj = $this->_paramLibCache->getById($testRunCommand->testParamLibraryId);
                     $defaultObj = $paramLibObj->getDefault();
                     $testRunCommand->setTarget($defaultObj->defaultValue);
                     $testRunCommand->setValue($paramLibObj->name);
                  }

               }

            }
         }

      } catch ( Exception $e ) {
         throw $e;
      }
   }

}
