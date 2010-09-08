<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Run/Builder.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );
require_once( 'CTM/Test/Run/BaseUrl/Selector.php' );
require_once( 'CTM/Test/Run/Browser/Selector.php' );

class CTM_Test_Run extends Light_Database_Object {
   public $id;
   public $test_suite_id;
   public $test_run_state_id;
   public $iterations;
   public $created_at;
   public $created_by;

   public function init() {
      $this->setSqlTable( 'test_run' );
      $this->setDbName( 'test' );
      $this->addOneToOneRelationship( 'Suite', 'CTM_Test_Suite', 'test_suite_id', 'id' );
   }

   public function remove() {

      if ( isset( $this->id ) ) {

         try {
            $command_sel = new CTM_Test_Run_Command_Selector();
            $command_and_params = array( new Light_Database_Selector_Criteria( 'test_run_id', '=', $this->id ) );
            $commands = $command_sel->find( $command_and_params );
            foreach ( $commands as $command ) {
               $command->remove();
            }
            $baseurl_sel = new CTM_Test_Run_BaseUrl_Selector();
            $baseurl_and_params = array( new Light_Database_Selector_Criteria( 'test_run_id', '=', $this->id ) );
            $baseurls = $baseurl_sel->find( $baseurl_and_params );
            foreach ( $baseurls as $baseurl ) {
               $baseurl->remove();
            }
            $browser_sel = new CTM_Test_Run_Browser_Selector();
            $browser_and_params = array( new Light_Database_Selector_Criteria( 'test_run_id', '=', $this->id ) );
            $browsers = $browser_sel->find( $browser_and_params );
            foreach ( $browsers as $browser ) {
               $browser->remove();
            }
         } catch ( Exception $e ) {
            throw $e;
         }
      }

      parent::remove();

   }

   public function createTestRunCommands() {
      // we need to make some test_run_commands
      if ( ! isset( $this->id ) ) {
         return;
      }
      // create a builder object for this Test_Run
      $ctm_run_builder = new CTM_Test_Run_Builder();
      $ctm_run_builder->build( $this );
   }

   public function createTestSuite() {
      if ( ! isset( $this->id ) ) {
         return;
      }
      // create a builder object for this Test_Run
      $ctm_run_builder = new CTM_Test_Run_Builder();
      $ctm_run_builder->buildTestSuite( $this );
   }

}
