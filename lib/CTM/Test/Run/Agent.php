<?php

require_once( 'Testing/Selenium.php' );
require_once( 'Light/CommandLine/Script.php' );
require_once( 'CTM/Test/Machine/Selector.php' );
require_once( 'CTM/Test/Run/Browser/Selector.php' );
require_once( 'CTM/Test/Run/Baseurl/Selector.php' );
require_once( 'CTM/Test/Browser/Cache.php' );

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
         try {

            // lookup our test machine.
            $test_machine = null;

            $machine_sel = new CTM_Test_Machine_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_machine_id ) );
            $test_machines = $machine_sel->find( $and_params );

            if ( isset( $test_machines[0] ) ) {
               $test_machine = $test_machines[0];

               while( $this->_doWork( $test_machine ) == false ) {
                  $this->message( 'sleeping- waiting for work.' );
                  sleep(60);
               }

               $this->done(0);

            }
            
            $this->message( 'Failed to find test machine: ' . $test_machine_id );
            $this->done(255);

         } catch ( Exception $e ) {
            $this->message( 'failed with e: ' . print_r( $e, true ) );
            $this->done( 255 );
         }
         $this->done(0);
      }

      $this->message( 'We failed to start up against the test_machine_id provided: ' . $test_machine_id );
      $this->done(255);

   }

   function _doWork( CTM_Test_Machine $test_machine ) {
      try {

         var_dump( $test_machine );

         // scan to see if there are any test runs waiting on us if not sleep for a bit.
         $find_browser_sel = new CTM_Test_Run_Browser_Selector();
         
         $and_params = array( 
               new Light_Database_Selector_Criteria( 'test_machine_id', '=', $test_machine->id ),
               new Light_Database_Selector_Criteria( 'test_run_state_id', '=', 1 ),
         );
         
         $test_run_browsers = $find_browser_sel->find( $and_params ); 
         
         if ( isset( $test_run_browsers[0] ) ) {
            // process the first one and exit.
            $test_run_browser = $test_run_browsers[0];

            // lookup the browser associated to the test.
            $browser_cache = new CTM_Test_Browser_Cache();
            $test_browser = $browser_cache->getById( $test_run_browser->test_browser_id );

            var_dump( $test_browser );

            $s_browser = null;
            if ( $test_browser->name == 'Chrome' ) {
               $s_browser = '*chrome';
            }
            $s_browser = '*firefox';

            $s_url = 'http://jorcutt-laptop/';

            $selenium_obj = new Testing_Selenium( $s_browser, $s_url, $test_machine->ip );
            $selenium_obj->start();
            $selenium_obj->open( $s_url );
            if ( $this->selenium->getLocation() != $s_url ) {
               $this->message( 'FAIL: failed to open starting url: $s_url' );
            } else {
               // loop through all the test commands.
            }
            $selenium_obj->stop();

            // We did work tell them such!
            return true;
         } else {
            $this->message( 'no work found' );
            return false;
         }

      } catch ( Exception $e ) {
         $this->message( 'exception caught: ' . print_r( $e, true ) );
         return false;
      }

   }

}
