<?php

require_once( 'CTM/Test/Machine/Selector.php' );
require_once( 'CTM/Test/Run/Agent.php' );
require_once( 'CTM/Test/Runner/Agent.php' );
require_once( 'Light/CommandLine.php' );

//--------------------------------------------------------------------------------
//--------------------------------------------------------------------------------
class CTM_Test_Runner extends Light_CommandLine {
   private $_test_agents;

   function __construct() {
      $this->_test_agents = array();
      parent::__construct();
   }

   public function run() {
      $this->message( 'CTM_Test_Runner starting up' );
      $do_not_exit = true;
      while( $do_not_exit == true ) {

         try {

            $test_machine_sel = new CTM_Test_Machine_Selector()
               ;
            $and_params = array( new Light_Database_Selector_Criteria( 'is_disabled', '=', 0 ) );

            $test_machines = $test_machine_sel->find( $and_params );
            
            foreach ( $test_machines as $test_machine ) {
               if ( isset( $this->_test_agents[ $test_machine->id ] ) ) {
                  // is it still running?
                  $test_agent = $this->_test_agents[ $test_machine->id ];
                  
                  if ( $test_agent->isRunning() == true ) {
                     $this->message( 'test_machine[' . $test_machine->id . ']: is still running' );
                  } else {
                     $this->message( 'test_machine[' . $test_machine->id . ']: is being restarted' );
                     $this->_test_agents[ $test_machine->id ] = null;
                     $this->_test_agents[ $test_machine->id ] = new CTM_Test_Runner_Agent( $test_machine->id );
                     $this->_test_agents[ $test_machine->id ]->run();
                  }

               } else {
                  // create the test agent
                  $this->message( 'test_machine[' . $test_machine->id . ']: is starting up' );
                  $this->_test_agents[ $test_machine->id ] = new CTM_Test_Runner_Agent( $test_machine->id );
                  $this->_test_agents[ $test_machine->id ]->run();
               }
            }

            $this->message( 'sleeping 60 seconds...' );
            sleep( 60 );

         } catch ( Exception $e ) {
         }

      }
   }

}
