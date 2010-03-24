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
  
   private function _findMachineByHostname( $hostname ) {
      try {
         $sel = new CTM_Test_Machine_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'hostname', '=', $hostname ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
         return null;
      } catch ( Exception $e ) {
      }
      return null;
   }

   public function handleRequest() {
      $hostname         = $this->getOrPost( 'hostname', '' );
      $os               = $this->getOrPost( 'os', '' );
      $chrome           = $this->getOrPost( 'chrome', '' );
      $chrome_version   = $this->getOrPost( 'chrome_version', '' );
      $firefox          = $this->getOrPost( 'firefox', '' );
      $firefox_version  = $this->getOrPost( 'firefox_version', '' );
      $safari           = $this->getOrPost( 'safari', '' );
      $safari_version   = $this->getOrPost( 'safari_version', '' );

      if ( $hostname == '' ) {
         echo "hostname is required!\n";
         return false;
      }

      // Since PHP is bastard for OS detection - so we need ghetto detection here.
      // TODO: jeo this may / may not be needed

      // see if there is a test machine available for this hostname.
      $test_machine = $this->_findMachineByHostname( $hostname );

      try {
         if ( $test_machine == null ) {

            $new = new CTM_Test_Machine();
            $new->hostname       = $hostname;
            $new->os             = $os;
            $new->created_at     = time();
            $new->last_modified  = time();
            $new->is_disabled    = 0;
            $new->save();

            $test_machine = $this->_findMachineByHostname( $hostname );

            if ( $test_machine == null ) {
               echo "failed to create\n";
            }

         } else {
            $test_machine->last_modified = time();
            $test_machine->save();
         }

      } catch ( Exception $e ) {
         echo "failed to find test_machine\n";
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
         }

         // okay so we have a test_machine save up browsers...
         if ( $chrome == 'yes' ) {
            // 5.0.307.11 - major.minor.patch.subversion --? what the if anyone wants to figure that out have at it.
            if ( preg_match( '/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $chrome_version, $version_preg ) ) {
               $browser = $this->_findBrowser( 'Chrome', (int) $version_preg[1], (int) $version_preg[2], (int) $version_preg[3] );

               // associate this browser to the machine
               try {
                  $browser_link = new CTM_Test_Machine_Browser();
                  $browser_link->test_machine_id = $test_machine->id;
                  $browser_link->test_browser_id = $browser->id;
                  $browser_link->save();
               } catch ( Exception $e ) {
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
               }

            }
         }

         echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
         echo '<ctm_test_machine>' . "\n";
         echo '   <id>' . $test_machine->id . '</id>' . "\n";
         echo '   <hostname>' . $test_machine->hostname . '</hostname>' . "\n";
         echo '   <created_at>' . $test_machine->created_at . '</created_at>' . "\n";
         echo '   <last_modified>' . $test_machine->last_modified . '</last_modified>' . "\n";
         echo '   <is_disabled>' . $test_machine->is_disabled . '</is_disabled>' . "\n";
         echo '</ctm_test_machine>' . "\n";

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
            // return the browser =)
            return $rows[0];
         } else {
            $browser = new CTM_Test_Browser();
            $browser->name = $name;
            $browser->major_version = $major;
            $browser->minor_version = $minor;
            $browser->patch_version = $patch;
            $browser->save();
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
