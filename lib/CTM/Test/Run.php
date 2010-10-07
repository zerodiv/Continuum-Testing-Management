<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Run/Builder.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Run/BaseUrl/Selector.php' );
require_once( 'CTM/Test/Run/Browser/Selector.php' );

class CTM_Test_Run extends Light_Database_Object
{
   public $id;
   public $testSuiteId;
   public $testRunStateId;
   public $iterations;
   public $createdAt;
   public $createdBy;

   public function init()
   {
      $this->setSqlTable('ctm_test_run');
      $this->setDbName('test');
      $this->addOneToOneRelationship('Suite', 'CTM_Test_Suite', 'testSuiteId', 'id');
   }

   public function remove()
   {

      if ( isset( $this->id ) ) {

         try {
            $commandSel = new CTM_Test_Run_Command_Selector();
            $commandAndParams = array( new Light_Database_Selector_Criteria( 'testRunId', '=', $this->id ) );
            $commands = $commandSel->find($commandAndParams);
            foreach ( $commands as $command ) {
               $command->remove();
            }
            $baseurlSel = new CTM_Test_Run_BaseUrl_Selector();
            $baseurlAndParams = array( new Light_Database_Selector_Criteria( 'testRunId', '=', $this->id ) );
            $baseurls = $baseurlSel->find($baseurlAndParams);
            foreach ( $baseurls as $baseurl ) {
               $baseurl->remove();
            }
            $browserSel = new CTM_Test_Run_Browser_Selector();
            $browserAndParams = array( new Light_Database_Selector_Criteria( 'testRunId', '=', $this->id ) );
            $browsers = $browserSel->find($browserAndParams);
            foreach ( $browsers as $browser ) {
               $browser->remove();
            }
         } catch ( Exception $e ) {
            throw $e;
         }
      }

      parent::remove();

   }

   public function createTestRunCommands()
   {
      // we need to make some test_run_commands
      if ( ! isset( $this->id ) ) {
         return;
      }
      // create a builder object for this Test_Run
      $ctmRunBuilder = new CTM_Test_Run_Builder();
      $ctmRunBuilder->build($this);
   }

   public function createTestSuite()
   {
      if ( ! isset( $this->id ) ) {
         return;
      }
      // create a builder object for this Test_Run
      $ctmRunBuilder = new CTM_Test_Run_Builder();
      $ctmRunBuilder->buildTestSuite($this);
   }

}
