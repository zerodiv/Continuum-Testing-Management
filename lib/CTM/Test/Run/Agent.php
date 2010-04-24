<?php

require_once( 'Testing/Selenium.php' );
require_once( 'Light/CommandLine/Script.php' );
require_once( 'CTM/Test/Browser/Cache.php' );
require_once( 'CTM/Test/Machine/Cache.php' );
require_once( 'CTM/Test/Run/BaseUrl/Cache.php' );
require_once( 'CTM/Test/Run/Browser/Selector.php' );
require_once( 'CTM/Test/Run/Command/Selector.php' );

class CTM_Test_Run_Agent extends Light_CommandLine_Script {
   private $_test_run_baseurl_cache;
   private $_test_browser_cache;
   private $_selenium_objects;

   public function init() {
      $this->arguments()->addIntegerArgument( 'test_machine_id', 'The test machine that this process is feeding to.' )->setIsRequired( true );
   }

   public function run() {

      // init objects needed to do our work.
      $this->_selenium_objects = array();

      $this->_test_run_baseurl_cache = new CTM_Test_Run_BaseUrl_Cache();
      $this->_test_browser_cache = new CTM_Test_Browser_Cache();
      $this->_test_machine_cache = new CTM_Test_Machine_Cache();

      $arg = $this->arguments()->getArgument( 'test_machine_id' );

      if ( ! isset( $arg ) ) {
         $this->message( 'we failed to find a test_machine_id argument' );
         $this->done(255);
      }
    
      $test_machine_id = $arg->getValue();

      if ( $test_machine_id > 0 ) {
         try {

            // lookup our test machine.
            $test_machine = $this->_test_machine_cache->getById( $test_machine_id );

            if ( isset( $test_machine ) ) {

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

   private function _runBrowser( CTM_Test_Run_Browser $test_run_browser ) {

      try {

         // snag the test machine for the ip / host information.
         $test_machine = $this->_test_machine_cache->getById( $test_run_browser->test_machine_id );

         if ( ! isset( $test_machine->id ) ) {
            $this->message( 'Failed to _runBrowser: unable to resolve test_machine_id: ' . $test_run_browser->test_machine_id );
            return false;
         }

         // snag the target web browser information
         $test_browser = $this->_test_browser_cache->getById( $test_run_browser->test_browser_id );

         if ( ! isset( $test_browser->id ) ) {
            $this->message( 'Failed to _runBrowser: unable to resolve test_browser_id: ' . $test_run_browser->test_browser_id );
            return false;
         }

         // pull the test_run_command stack off.
         $command_sel = new CTM_Test_Run_Command_Selector();
         $and_params = array( Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run_browser->test_run_id ) );
         $or_params = array();
         $order_by = array( 'id' );

         $test_run_commands = $command_sel->find( $and_params, $or_params, $order_by );

         if ( count( $test_run_commands ) == 0 ) {
            $this->message( 'Test plan is empty somehow - zombies ate it - Marking completed' );
            $test_run_browser->test_run_state_id = 3; // complete
            $test_run_browser->save();
            return true;
         }

         $current_test_suite_id  = $test_run_commands[0]->test_suite_id;
         $current_test_id        = $test_run_commands[0]->test_id;

         // start selenium specific code.
         $selenium_browser = null;
         if ( $test_browser->name == 'Chrome' ) {
            $selenium_browser = '*chrome';
         }

         // TODO: Currently overriding the browser setting chrome was craping itself 04/2010 - so 
         // effectivly we only support firefox atm.
         $selenium_browser = '*firefox';

         $selenium_url = $this->_test_run_baseurl_cache = getByCompoundKey( 
               $test_run_browser->test_run_id,
               $current_test_suite_id,
               $current_test_id
         );

         $selenium_starting_url = $starting_url->baseurl;
         $current_url = $selenium_starting_url;

         //--------------------------------------------------------------------------------
         // TODO: This needs to be gutted and changed to a command stack where each of the distinct
         // urls are queued up so we can setup / tear down a session without harming each other.
         //--------------------------------------------------------------------------------
         $selenium_obj = new Testing_Selenium( $slenium_browser, $selenium_url, $test_machine->ip );
         $selenium_obj->start();
         $selenium_obj->open( $selenium_url );

         if ( $this->selenium->getLocation() != $selenium_url ) {
            $this->message( 'FAIL: failed to open starting url: $s_url' );
            $selenium_obj->stop();
            $test_run_browser->test_run_state_id = 5; // failed
            $test_run_browser->save();
         } else {
            // loop through all the test commands.
            foreach ( $test_run_commands as $test_run_command ) {

               $possible_url_change = false;
               if ( $current_test_suite_id == $test_run_command->test_suite_id ) {
                  // no change.
               } else {
                  $possible_url_change = true;
               }
               if ( $current_test_id == $test_run_command->test_id ) {
                  // no change.
               } else {
                  $possible_url_change = true;
               }

            }
         }

         $selenium_obj->stop();


      } catch ( Exception $e ) {
         $this->message( 'Failed to _runBrowser: ' . print_r( $e, true ) );
         return false;
      }

      $this->message( 'Failed to _runBrowser: unknown error' );
      return false;
   }

   private function _doWork( CTM_Test_Machine $test_machine ) {
      try {

         // scan to see if there are any test runs waiting on us if not sleep for a bit.
         $find_browser_sel = new CTM_Test_Run_Browser_Selector();
         
         $and_params = array( 
               new Light_Database_Selector_Criteria( 'test_machine_id', '=', $test_machine->id ),
               new Light_Database_Selector_Criteria( 'test_run_state_id', '=', 1 ),
         );
         
         $test_run_browsers = $find_browser_sel->find( $and_params ); 
        
         if ( count( $test_run_browsers ) > 0 ) {
            foreach ( $test_run_browsers as $test_run_browser ) {
               $this->_runBrowser( $test_run_browser );
            }
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
