<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Run/Cache.php' );

class CTM_Test_Run_Browser extends Light_Database_Object
{
   public $id;
   public $testRunId;
   public $testBrowserId;
   public $testMachineId;
   public $testRunStateId;
   public $hasLog;

   public function init()
   {
      $this->setSqlTable('ctm_test_run_browser');
      $this->setDbName('test');
      $this->addOneToOneRelationship('Run', 'CTM_Test_Run', 'testRunId', 'id');
      $this->addOneToOneRelationship('Browser', 'CTM_Test_Browser', 'testBrowserId', 'id');
      $this->addOneToOneRelationship('Machine', 'CTM_Test_Machine', 'testMachineId', 'id');
      $this->addOneToOneRelationship('TestRunState', 'CTM_Test_Run_State', 'testRunStateId', 'id');
   }

   public function save()
   {

      // update our parent with our state too.
      try {
         $testRunCache = new CTM_Test_Run_Cache();
         $testRun = $testRunCache->getById($this->testRunId);

         if ( $testRun->testRunStateId == $this->testRunStateId ) {
            // if the state is already set save the trouble of the save()
         } else if ( $testRun->testRunStateId == 5 ) {
            // If the test run has failed we cannot undo a failure.
         } else {
            $testRun->testRunStateId = $this->testRunStateId;
            $testRun->save();
         }

      } catch ( Exception $e ) {
      }

      // do the default save method.
      return parent::save();
   }

}
