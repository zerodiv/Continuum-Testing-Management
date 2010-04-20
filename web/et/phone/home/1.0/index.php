<?php

// bootstrap the include path
require_once( '../../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Browser.php' );
require_once( 'CTM/Test/Browser/Selector.php' );
require_once( 'CTM/Test/Machine.php' );
require_once( 'CTM/Test/Machine/Selector.php' );
require_once( 'CTM/Test/Machine/Browser.php' );
require_once( 'CTM/Test/Machine/Browser/Selector.php' );

class CTM_ET_Phone_Home_Main extends CTM_Site {

   public function setupPage() {
      $this->_pagetitle = 'Main';
      return true;
   } 
  
   private function _findMachineByGuid( $guid ) {
      try {
         $sel = new CTM_Test_Machine_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'guid', '=', $guid ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
         return null;
      } catch ( Exception $e ) {
      }
      return null;
   }

   private function _serviceOutput( $status, $message, $test_machine = null ) {
      echo "<?xml version=\"1.0\"?>\n";
      echo "<etResponse>\n";
      echo "   <version>1.0</version>\n";
      echo "   <status>$status</status>\n";
      echo "   <message>$message</message>\n";
      if ( is_object( $test_machine ) ) {
         echo '   <id>' . $test_machine->id . '</id>' . "\n";
         echo '   <created_at>' . $test_machine->created_at . '</created_at>' . "\n";
         echo '   <last_modified>' . $test_machine->last_modified . '</last_modified>' . "\n";
         echo '   <is_disabled>' . $test_machine->is_disabled . '</is_disabled>' . "\n";
      }
      echo "</etResponse>\n";


   }

   public function handleRequest() {

      $fh = fopen( '/tmp/jeo.txt', 'w');
      fputs( $fh, print_r( $GLOBALS, true ) );
      fclose( $fh );

      $guid             = $this->getOrPost( 'guid', '' );
      $ip               = $this->getOrPost( 'ip', '' );
      $os               = $this->getOrPost( 'os', '' );
      $ie               = $this->getOrPost( 'ie', '' );
      $ie_version       = $this->getOrPost( 'ie_version', '' );
      $chrome           = $this->getOrPost( 'chrome', '' );
      $chrome_version   = $this->getOrPost( 'chrome_version', '' );
      $firefox          = $this->getOrPost( 'firefox', '' );
      $firefox_version  = $this->getOrPost( 'firefox_version', '' );
      $safari           = $this->getOrPost( 'safari', '' );
      $safari_version   = $this->getOrPost( 'safari_version', '' );

      if ( $guid == '' ) {
         $this->_serviceOutput( 'FAIL', "guid is required!" );
         return false;
      }

      if ( $ip == '' ) {
         $this->_serviceOutput( 'FAIL', "ip is required!" );
         return false;
      }

      if ( $os == '' ) {
         $this->_serviceOutput( 'FAIL', "os is required!" );
         return false;
      }

      // see if there is a test machine available for this hostname.
      $test_machine = $this->_findMachineByGuid( $guid );

      // if the test machine isn't found try adding it to the system.
      try {
         if ( ! isset( $test_machine ) ) {

            $new = new CTM_Test_Machine();
            $new->guid           = $guid;
            $new->ip             = $ip;
            $new->os             = $os;
            $new->created_at     = time();
            $new->last_modified  = time();
            $new->is_disabled    = 0;
            $new->save();

            $test_machine = $this->_findMachineByGuid( $guid );

            if ( ! isset( $test_machine ) ) {
               $this->_serviceOutput( 'FAIL', "failed to create test machine" );
               return false;
            }

         } else {
            $test_machine->last_modified = time();
            $test_machine->save();
         }

      } catch ( Exception $e ) {
         $this->_serviceOutput( 'FAIL', "failed to find test_machine" );
         return false;
      }

      if ( isset( $test_machine ) ) {

         try {
            $sel = new CTM_Test_Machine_Browser_Selector();
            $and_params = array( 
               new Light_Database_Selector_Criteria( 'test_machine_id', '=', $test_machine->id ),
            );
            $machine_browsers = $sel->find( $and_params );

            if ( count( $machine_browsers ) > 0 ) {
               foreach ( $machine_browsers as $machine_browser ) {
                  $machine_browser->remove();
               }
            }

         } catch ( Exception $e ) {
            $this->_serviceOutput( 'FAIL', "failed to find test_machine browsers" );
            return false;
         }

         // okay so we have a test_machine save up browsers...
         if ( $ie == 'yes' ) {
            if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)$/', $ie_version, $version_preg ) ) {
               $browser = $this->_findBrowser( 'Internet Explorer', (int) $version_preg[1], (int) $version_preg[2], (int) $version_preg[3] );

               // associate this browser to the machine
               try {
                  $browser_link = new CTM_Test_Machine_Browser();
                  $browser_link->test_machine_id = $test_machine->id;
                  $browser_link->test_browser_id = $browser->id;
                  $browser_link->save();
               } catch ( Exception $e ) {
                  $this->_serviceOutput( 'FAIL', "failed to update test_machine browser for IE" );
                  return false;
               }
            
            }
         }

         if ( $chrome == 'yes' ) {
            // 5.0.307.11 - major.minor.patch.subversion --? what the if anyone wants to figure that out have at it.
            if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)$/', $chrome_version, $version_preg ) ) {
               $browser = $this->_findBrowser( 'Chrome', (int) $version_preg[1], (int) $version_preg[2], (int) $version_preg[3] );

               // associate this browser to the machine
               try {
                  $browser_link = new CTM_Test_Machine_Browser();
                  $browser_link->test_machine_id = $test_machine->id;
                  $browser_link->test_browser_id = $browser->id;
                  $browser_link->save();
               } catch ( Exception $e ) {
                  $this->_serviceOutput( 'FAIL', "failed to update test_machine browser for chrome" );
                  return false;
               }
            
            }
         }

         if ( $firefox == 'yes' ) {
            if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)$/', $firefox_version, $version_preg ) ) {
               $browser = $this->_findBrowser( 'Firefox', (int) $version_preg[1], (int) $version_preg[2], (int) $version_preg[3] );

               // associate this browser to the machine?
               try {
                  $browser_link = new CTM_Test_Machine_Browser();
                  $browser_link->test_machine_id = $test_machine->id;
                  $browser_link->test_browser_id = $browser->id;
                  $browser_link->save();
               } catch ( Exception $e ) {
                  $this->_serviceOutput( 'FAIL', "failed to update test_machine browser for firefox" );
                  return false;
               }

            }
         }

         if ( $safari == 'yes' ) {
            // safari comes in major.minor.patch (ints)
            if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)$/', $safari_version, $version_preg ) ) {
               $browser = $this->_findBrowser( 'Safari', (int) $version_preg[1], (int) $version_preg[2], (int) $version_preg[3] );

               // associate this browser to the machine?
               try {
                  $browser_link = new CTM_Test_Machine_Browser();
                  $browser_link->test_machine_id = $test_machine->id;
                  $browser_link->test_browser_id = $browser->id;
                  $browser_link->save();
               } catch ( Exception $e ) {
                  $this->_serviceOutput( 'FAIL', "failed to update test_machine browser for safari" );
                  return false;
               }

            }
         }

         $this->_serviceOutput( 'OK', '', $test_machine );

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
               new Light_Database_Selector_Criteria( 'major_version', '=', $major ),
               new Light_Database_Selector_Criteria( 'minor_version', '=', $minor ),
               new Light_Database_Selector_Criteria( 'patch_version', '=', $patch ),
         );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            // return the browser
            $browser = $rows[0];
            $browser->is_available = true;
            $browser->last_seen = time();
            $browser->save();
            return $browser;
         } else {
            $browser = new CTM_Test_Browser();
            $browser->name = $name;
            $browser->major_version = $major;
            $browser->minor_version = $minor;
            $browser->patch_version = $patch;
            $browser->is_availble = true;
            $browser->last_seen = time();
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
