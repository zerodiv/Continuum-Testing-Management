<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Run/Builder.php' );

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

}
