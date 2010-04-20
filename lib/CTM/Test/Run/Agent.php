<?php

require_once( 'Light/CommandLine/Script.php' );
require_once( 'CTM/Test/Run/Machine/Selector.php' );

class CTM_Test_Run_Agent extends Light_CommandLine_Script {
   public function init() {
      $this->arguments()->addIntegerArgument( 'test_machine_id', 'The test machine that this process is feeding to.' )->setIsRequired( true );
   }
   public function run() {

      $arg = $this->arguments()->getArgument( 'test_machine_id' );

      if ( ! isset( $arg ) ) {
         $this->message( 'we failed to find a test_machine_id argument' );
         $this->done(255);
      }
    
      $test_machine_id = $arg->getValue();

      if ( $test_machine_id > 0 ) {
         // JEO: So this is a extremly paranoid way to handle this, but after we process a test runs
         // and then exit force deallocate all memory associated to the runner.
         try {

            // scan to see if there are any test runs waiting on us if not sleep for a bit.


         } catch ( Exception $e ) {
            $this->message( 'failed with e: ' . print_r( $e, true ) );
            $this->done( 255 );
         }
         $this->done(0);
      }

      $this->message( 'We failed to start up against the test_machine_id provided: ' . $test_machine_id );
      $this->done(255);

   }
}
