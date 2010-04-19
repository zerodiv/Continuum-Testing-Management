<?php

require_once( 'CTM/Test/Runner/Agent/Config.php' );

class CTM_Test_Runner_Agent {
   private $_test_machine_id;
   private $_process_handle;
   private $_process_pipes;

   function __construct( $test_machine_id ) {
      $this->_test_machine_id = $test_machine_id;
   }

   public function getTestMachineId() {
      return $this->_test_machine_id;
   }

   public function run() {

      // setup the pipes to drain data into.
      $pipes = array();
      $pipes[0] = array( 'pipe', 'r' );
      $pipes[1] = array( 'pipe', 'w' );
      $pipes[2] = array( 'pipe', 'r' );

      $this->_process_pipes = array();

      // echo "run me: " .  CTM_Test_Runner_Agent_Config::TEST_RUNNER() . "\n";

      $this->_process_handle = proc_open(
            CTM_Test_Runner_Agent_Config::TEST_RUNNER() . ' --test_machine_id=' . $this->_test_machine_id,
            $pipes,
            $this->_process_pipes
      );

   }

   public function isRunning() {
      if ( is_resource( $this->_process_handle ) ) {
         $proc_status = proc_get_status( $this->_process_handle );
         if ( $proc_status['running'] == true ) {
            return true;
         }
      }
      return false;
   }

}
