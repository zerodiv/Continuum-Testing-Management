<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Run/Builder.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );

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

   public function remove() {

      if ( isset( $this->id ) ) {

         try {
            $sel = new CTM_Test_Run_Command_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_run_id', '=', $this->id ) );
            $commands = $sel->find( $and_params );
            foreach ( $commands as $command ) {
               $command->remove();
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

}
