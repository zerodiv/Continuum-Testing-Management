<?php

// bootstrap the include path
require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Browser.php' );
require_once( 'CTM/Test/Browser/Selector.php' );

// require_once( 'CTM/Test/Machine.php' );
// require_once( 'CTM/Test/Machine/Selector.php' );
require_once( 'CTM/Test/Machine/Cache.php' );

require_once( 'CTM/Test/Machine/Browser.php' );
require_once( 'CTM/Test/Machine/Browser/Selector.php' );

require_once( 'CTM/Test/Run/Cache.php' );
require_once( 'CTM/Test/Run/Browser/Selector.php' );

class CTM_ET_Phone_Home_Main extends CTM_Site {

   public function setupPage() {
      $this->_testMachineCache = new CTM_Test_Machine_Cache();
      return true;
   } 
  
   private function _findBrowserByMachine( $machine_id, $browser_id ) {
      try {
         $sel = new CTM_Test_Machine_Browser_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'testMachineId', '=', $machine_id ),
               new Light_Database_Selector_Criteria( 'testBrowserId', '=', $browser_id )
         );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
         return null;
      } catch ( Exception $e ) {
      }
      return null;
   }


   private function _associateBrowserToMachine( CTM_Test_Machine $test_machine, $browserName ) {
      $browser_exists   = $this->getOrPost( $browserName, null );
      $browser_version  = $this->getOrPost( $browserName . '_version', null );

      // CTM_Test_Browser $browser ) {
      try {

         if ( $browser_exists != 'yes' ) {
            return false;
         }
        
         $browser = null;
         if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)$/', $browser_version, $version_preg ) ) {
            $browser = $this->_findBrowser( $browserName, (int) $version_preg[1], (int) $version_preg[2], (int) $version_preg[3] );
         } else {
            return false;
         }
         

         $browser_link = $this->_findBrowserByMachine( $test_machine->id, $browser->id );
         if ( $browser_link == null ) { 
            $browser_link = new CTM_Test_Machine_Browser();
         }
         $browser_link->testMachineId = $test_machine->id;
         $browser_link->testBrowserId = $browser->id;
         $browser_link->isAvailable = 1;
         $browser_link->lastSeen = time();
         $browser_link->save();
      } catch ( Exception $e ) {
         $this->_serviceOutput( 'FAIL', "failed to update test_machine browser for: " . $browser->name );
         return false;
      }
   }

    private function _serviceOutput($status, $message, $test_machine = null, $testRuns = null ) {

       
       $writer = new XMLWriter();
       $writer->openMemory();
       $writer->setIndent(true);
       $writer->startDocument( '1.0', 'UTF-8' );

       $writer->startElement( 'etResponse' );

       $writer->writeElement( 'version', '2.0' );
       $writer->writeElement( 'status', $status );
       $writer->writeElement( 'message', $message );

       // Add the CTM_Test_Machine object.
       if ( is_object( $test_machine ) && get_class( $test_machine ) == 'CTM_Test_Machine' ) {
         $test_machine->toXML( $writer );
       }

       $writer->startElement( 'Runs' );
       if ( is_array( $testRuns ) && count( $testRuns ) > 0 ) {
          foreach ( $testRuns as $testRun ) {
             $testRun->toXml( $writer );
          }
       }
       $writer->endElement();

       $writer->endElement();
       $writer->endDocument();
       return $writer->outputMemory( true );
    }


   public function handleRequest() {

      header( 'Content-Type: text/xml' );

      /*
      $fh = fopen('/tmp/jeo.txt', 'w');
      fputs($fh, print_r($GLOBALS, true));
      fclose($fh);
      */

      $guid             = $this->getOrPost('guid', '');
      $ip               = $_SERVER['REMOTE_ADDR'];
      $os               = $this->getOrPost('os', '');
      $machineName      = $this->getOrPost('machine_name', '' );

      if ( $guid == '' ) {
         echo $this->_serviceOutput( 'FAIL', "guid is required!" );
         return false;
      }

      if ( $ip == '' ) {
         echo $this->_serviceOutput( 'FAIL', "ip is required!" );
         return false;
         return false;
      }

      if ( $os == '' ) {
         echo $this->_serviceOutput( 'FAIL', "os is required!" );
         return false;
      }

      // see if there is a test machine available for this hostname.
      $test_machine = $this->_testMachineCache->getByGuid( $guid );

      // if the test machine isn't found try adding it to the system.
      try {
         if ( ! isset( $test_machine ) ) {

            $new = new CTM_Test_Machine();
            $new->guid           = $guid;
            $new->ip             = $ip;
            $new->os             = $os;
            $new->machineName   = $machineName;
            $new->createdAt     = time();
            $new->lastModified  = time();
            $new->isDisabled    = 0;
            $new->save();

            $test_machine = $this->_testMachineCache->getByGuid( $guid );

            if ( ! isset( $test_machine ) ) {
               echo $this->_serviceOutput( 'FAIL', "failed to create test machine" );
               return false;
            }

         } else {
            $test_machine->ip = $ip;
            $test_machine->machineName = $machineName;
            $test_machine->lastModified = time();
            $test_machine->save();
         }

      } catch ( Exception $e ) {
         echo $this->_serviceOutput( 'FAIL', "failed to find test_machine" );
         return false;
      }

      if ( isset( $test_machine ) ) {

         try {
            $sel = new CTM_Test_Machine_Browser_Selector();
            $and_params = array( 
               new Light_Database_Selector_Criteria( 'testMachineId', '=', $test_machine->id ),
            );
            $machine_browsers = $sel->find( $and_params );

            // disable all of the browsers.
            if ( count( $machine_browsers ) > 0 ) {
               foreach ( $machine_browsers as $machine_browser ) {
                  $machine_browser->isAvailable = 0;
                  $machine_browser->save();
               }
            }

         } catch ( Exception $e ) {
            echo $this->_serviceOutput( 'FAIL', "failed to find test_machine browsers" );
            return false;
         }

         // okay so we have a test_machine save up browsers...
         $this->_associateBrowserToMachine( $test_machine, 'iexplore' );
         $this->_associateBrowserToMachine( $test_machine, 'googlechrome' );
         $this->_associateBrowserToMachine( $test_machine, 'firefox' );
         $this->_associateBrowserToMachine( $test_machine, 'safari' );

         // let's find any work for them and push it.

         // let's get the first test available browser run for this machine
         $testRunBrowserSel = new CTM_Test_Run_Browser_Selector();

         $and_params = array(
               new Light_Database_Selector_Criteria('testMachineId', '=', $test_machine->id),
               new Light_Database_Selector_Criteria('testRunStateId', '=', 1), // queued
         ); 
         
         $queued_rows = $testRunBrowserSel->find($and_params, array(), array('id'), 1); 
         
         // pick up any work that might of failed in progress
         $and_params = array(
               new Light_Database_Selector_Criteria('testMachineId', '=', $test_machine->id),
               new Light_Database_Selector_Criteria('testRunStateId', '=', 2), // executing
         );
                
         $executing_rows = $testRunBrowserSel->find($and_params, array(), array('id'), 1); 
         
         $testRunBrowserRows = array();
         $testRunBrowserRows = array_merge( $executing_rows, $queued_rows );

         if ( count($testRunBrowserRows) > 0 ) {
            // mark all the tests as executing.
            foreach ( $testRunBrowserRows as $testRunBrowser ) {
               $testRunBrowser->testRunStateId = 2; // executing.
               $testRunBrowser->save();
            }
         }

         echo $this->_serviceOutput( 'OK', '', $test_machine, $testRunBrowserRows );
         return false;
      }

      echo "invalid request, goodbye.\n";
      return false;

   } 
   
   private function _findBrowser( $name, $major, $minor, $patch ) {
      try {
         $sel = new CTM_Test_Browser_Selector();
         $and_params = array( 
               new Light_Database_Selector_Criteria( 'name', '=', $name ),
               new Light_Database_Selector_Criteria( 'majorVersion', '=', $major ),
               new Light_Database_Selector_Criteria( 'minorVersion', '=', $minor ),
               new Light_Database_Selector_Criteria( 'patchVersion', '=', $patch ),
         );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            // return the browser
            $browser = $rows[0];
            $browser->isAvailable = true;
            $browser->lastSeen = time();
            $browser->save();
            return $browser;
         } else {
            $browser = new CTM_Test_Browser();
            $browser->name = $name;
            $browser->majorVersion = $major;
            $browser->minorVersion = $minor;
            $browser->patchPersion = $patch;
            $browser->isAvailable = true;
            $browser->lastSeen = time();
            $browser->save();
            if ( $browser->id > 0 ) {
               return $browser;
            }
         }
      } catch ( Exception $e ) {
      }
   }

   public function displayBody() {
      return true;
   }

}

$mainPage = new CTM_ET_Phone_Home_Main();
$mainPage->displayPage();
