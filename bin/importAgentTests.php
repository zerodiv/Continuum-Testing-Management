#!/usr/bin/php -q
<?php

require_once( '../bootstrap.php' );
require_once( 'Light/Config.php' );
require_once( 'Light/CommandLine/Script.php' );

require_once( 'CTM/User/Cache.php' );

require_once( 'CTM/Test/Folder/Cache.php' );
require_once( 'CTM/Test/Folder/Selector.php' );

require_once( 'CTM/Test/Suite/Cache.php' );
require_once( 'CTM/Test/Suite/Plan/Selector.php' );

require_once( 'CTM/Test/Selector.php' );

/**
 * CTM_Regression_ImportAgent 
 * 
 * @uses Light
 * @uses _Commandline_Script
 * @package Platform
 * @version $Id: $
 * @copyright  Adicio 
 * @author $Author: $ 
 * @license 
 */
class CTM_Regression_ImportAgent extends Light_Commandline_Script
{
   const CTM_ADMIN_USER_ID = 1;
   const CTM_REGRESSION_FOLDER_NAME = 'CTM - Regression';
   const CTM_REGRESSION_SUITE_NAME = 'CTM - Regression Suite';

   private $_adminUserObj;
   private $_regressionFolderObj;
   private $_regressionSuiteObj;
   private $_planCounter;
   private $_testCounter;

   public function init()
   {
      $this->_planCounter = 1;
      $this->_testCounter = 0;
      $this->initAdminUser();
      $this->initRegressionFolder();
      $this->initRegressionSuite();
   }

   private function initAdminUser()
   {
      try {
         $userCacheObj = new CTM_User_Cache();
         $this->_adminUserObj = $userCacheObj->getById(self::CTM_ADMIN_USER_ID);
         if ( isset( $this->_adminUserObj->id ) && $this->_adminUserObj->id > 0 ) {
            $this->message("CTM Admin: " . $this->_adminUserObj->username . ' id: ' . $this->_adminUserObj->id);
            return true;
         }
         throw new Exception( 'Failed to load admin user by id: ' . self::CTM_ADMIN_USER_ID );
      } catch ( Exception $e ) {
         throw $e;
      }
   }

   public function initRegressionFolder()
   {
      try {
         $folderCacheObj = new CTM_Test_Folder_Cache();
         $this->_regressionFolderObj = $folderCacheObj->getByName(self::CTM_REGRESSION_FOLDER_NAME);
      
         if ( ! isset( $this->_regressionFolderObj ) ) {
            $this->_regressionFolderObj = new CTM_Test_Folder();
            $this->_regressionFolderObj->parentId = 1;
            $this->_regressionFolderObj->name = self::CTM_REGRESSION_FOLDER_NAME;
            $this->_regressionFolderObj->save();
         }

         if ( isset( $this->_regressionFolderObj ) && $this->_regressionFolderObj->id > 0 ) {
            $this->message(
                'Regression parent' .
                ' folder: ' .  $this->_regressionFolderObj->name . 
                ' id: ' . $this->_regressionFolderObj->id
            );
            return true;
         }

         throw new Exception( 'Failed to find regression folder named: ' . self::CTM_REGRESSION_FOLDER_NAME );

      } catch ( Exception $e ) {
         throw $e;
      }
   }

   private function initRegressionSuite()
   {
      try {
         $suiteCacheObj = new CTM_Test_Suite_Cache();

         $this->_regressionSuiteObj = $suiteCacheObj->getByName(self::CTM_REGRESSION_SUITE_NAME);

         if ( ! isset( $this->_regressionSuiteObj ) ) {
            $this->_regressionSuiteObj = new CTM_Test_Suite();
            $this->_regressionSuiteObj->testFolderId = $this->_regressionFolderObj->id;
            $this->_regressionSuiteObj->name = self::CTM_REGRESSION_SUITE_NAME;
            $this->_regressionSuiteObj->createdAt = time();
            $this->_regressionSuiteObj->createdBy = $this->_adminUserObj->id;
            $this->_regressionSuiteObj->modifiedAt = time();
            $this->_regressionSuiteObj->modifiedBy = $this->_adminUserObj->id;
            $this->_regressionSuiteObj->testStatusId = 1;
            $this->_regressionSuiteObj->save();
         }

         if ( isset( $this->_regressionSuiteObj ) && $this->_regressionSuiteObj->id > 0 ) {
            $this->message(
                'Regression' .
                ' suite name: ' .  $this->_regressionSuiteObj->name . 
                ' id: ' . $this->_regressionSuiteObj->id
            );
    
            $this->_regressionSuiteObj->removePlan();

            return true;
         }

         throw new Exception( 'Failed to lookup regression suite: ' . self::CTM_REGRESSION_SUITE_NAME );

      } catch ( Exception $e ) {
         throw $e;
      }

   }

   public function run()
   {
      $this->findTestDirs();
      $this->message('Added ' . $this->_testCounter . ' tests');
   }

   private function findTestDirs()
   {

      // scan the top level dir for suites to toss into the regression.
      $ctmBaseDir = Light_Config::get('CTM_Config', 'base_dir');
      $regressionDir = $ctmBaseDir . '/web/regression';

      $filesAndDirectories = scandir($regressionDir);

      foreach ( $filesAndDirectories as $fileOrDirectory ) {
         $testDir = $regressionDir . '/' . $fileOrDirectory;
         if ( $fileOrDirectory == '.' || $fileOrDirectory == '..' ) {
         } else if ( is_dir($testDir) ) {


            $folderSel = new CTM_Test_Folder_Selector();
            $folderAndParams = array(
                  new Light_Database_Selector_Criteria( 'parentId', '=', $this->_regressionFolderObj->id ),
                  new Light_Database_Selector_Criteria( 'name', '=', $fileOrDirectory )
            );

            $folderObjs = $folderSel->find($folderAndParams);

            $folderObj = null;

            // Look up the suite folder entry.
            if ( count($folderObjs) == 1 ) {
               $folderObj = $folderObjs[0];
            } else {
               $folderObj = new CTM_Test_Folder();
               $folderObj->parentId = $this->_regressionFolderObj->id;
               $folderObj->name = $fileOrDirectory;
               $folderObj->save();
            }

         
            $this->message(' Test Folder: ' . $testDir . ' id: ' . $folderObj->id);

            $this->processTestDir($testDir, $folderObj);

         }
      }
   }

   private function processTestDir( $testDir, CTM_Test_Folder $folderObj )
   {
      // Remove all existing tests in this folder
      $testSel = new CTM_Test_Selector();
      
      $testAndParams = array(
            new Light_Database_Selector_Criteria( 'testFolderId', '=', $folderObj->id )
      );

      $tests = $testSel->find($testAndParams); 

      foreach ( $tests as $test ) {
         $test->remove();
      }
      
      $suiteCacheObj = new CTM_Test_Suite_Cache(); 
      $suiteObj = $suiteCacheObj->getByName($folderObj->name);

      if ( isset( $suiteObj->id ) && $suiteObj->id > 0 ) {
         // remove their current plan.
         $suiteObj->removePlan();
      } else {
         $suiteObj = new CTM_Test_Suite();
         $suiteObj->testFolderId = $folderObj->id;
         $suiteObj->name = $folderObj->name;
         $suiteObj->createdAt = time();
         $suiteObj->createdBy = $this->_adminUserObj->id;
         $suiteObj->modifiedAt = time();
         $suiteObj->modifiedBy = $this->_adminUserObj->id;
         $suiteObj->testStatusId = 1;
         $suiteObj->save();
      }

      // read all the tests from the directory and shove them into the folder.
      $testFiles = scandir($testDir);
      $testCounter = 1;

      foreach ( $testFiles as $testItem ) {
         $testFile = $testDir . '/' . $testItem;
         
         if ( is_file($testFile) && preg_match('/.html$/', $testItem) ) {
            $this->message('testFile: ' . $testFile);

            $isDisabled = $testFile . '.disabled';
            if ( is_file($isDisabled) ) {
               $this->message('   !WARNING! - Test is disabled');
            } else {
               $testObj = new CTM_Test();
               $testObj->testFolderId = $folderObj->id;
               $testObj->name = $testItem;
               $testObj->testStatusId = 1;
               $testObj->createdAt = time();
               $testObj->createdBy = $this->_adminUserObj->id;
               $testObj->modifiedAt = time();
               $testObj->modifiedBy = $this->_adminUserObj->id;
               $testObj->revisionCount = 1;
               $testObj->save(); 
            
               if ( $testObj->id > 0 ) { // push the html in.
                  $this->message("   test: " . $testItem . " testId: " . $testObj->id);
                  $testObj->setHtmlSource($this->_adminUserObj, file_get_contents($testFile));
         
                  $testPlan = new CTM_Test_Suite_Plan();
                  $testPlan->testSuiteId = $suiteObj->id;
                  $testPlan->linkedId = $testObj->id;
                  $testPlan->testOrder = $testCounter;
                  $testPlan->testSuitePlanTypeId = 2; // this is a test
                  $testPlan->save();
                  $testCounter++;

                  if ( isset( $testPlan->id ) && $testPlan->id > 0 ) {
                     $this->message('   test added');
                     $this->_testCounter++;
                  }
               
               }

            }

         }

      }
     
      // fix up the test urls.
      $testSel = new CTM_Test_Selector();
      
      $testAndParams = array(
            new Light_Database_Selector_Criteria( 'testFolderId', '=', $folderObj->id )
      );

      $tests = $testSel->find($testAndParams); 

      // snag the config setting
      $ctmBaseUrl = Light_Config::get('CTM_Site_Config', 'base_url');

      foreach ( $tests as $test ) {
         // $this->message( 'base url: ' . $test->getBaseUrl()->baseurl );

         $testBaseUrl = str_replace('http://jorcutt-laptop', $ctmBaseUrl, $test->getBaseUrl()->baseurl);
         // $this->message( 'test url: ' . $testBaseUrl );

         $test->setBaseUrl($testBaseUrl);
      }

      $testPlan = new CTM_Test_Suite_Plan();
      $testPlan->testSuiteId = $this->_regressionSuiteObj->id;
      $testPlan->linkedId = $suiteObj->id;
      $testPlan->testOrder = ( $this->_planCounter );
      $testPlan->testSuitePlanTypeId = 1; // this is a suite
      $testPlan->save();
      $this->_planCounter++;

   }


}

$importObj = new CTM_Regression_ImportAgent();

exit();
