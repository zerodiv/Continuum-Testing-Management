<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Run/Cache.php' );

class CTM_Test_Run_Browser extends Light_Database_Object {
   public $id;
   public $test_run_id;
   public $test_browser_id;
   public $test_machine_id;
   public $test_run_state_id;
   public $has_log;

   public function init() {
      $this->setSqlTable( 'test_run_browser' );
      $this->setDbName( 'test' );
      $this->addOneToOneRelationship( 'Run', 'CTM_Test_Run', 'test_run_id', 'id' );
      $this->addOneToOneRelationship( 'Browser', 'CTM_Test_Browser', 'test_browser_id', 'id' );
      $this->addOneToOneRelationship( 'Machine', 'CTM_Test_Machine', 'test_machine_id', 'id' );
      $this->addOneToOneRelationship( 'TestRunState', 'CTM_Test_Run_State', 'test_run_state_id', 'id' );
   }

   public function save() {

      // update our parent with our state too.
      try {
         $testRunCache = new CTM_Test_Run_Cache();
         $test_run = $testRunCache->getById( $this->test_run_id );

         if ( $test_run->test_run_state_id == $this->test_run_state_id ) {
            // if the state is already set save the trouble of the save()
         } else if ( $test_run->test_run_state_id == 5 ) {
            // If the test run has failed we cannot undo a failure.
         } else {
            $test_run->test_run_state_id = $this->test_run_state_id;
            $test_run->save();
         }

      } catch ( Exception $e ) {
      }

      // do the default save method.
      return parent::save();
   }

}
