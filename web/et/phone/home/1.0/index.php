<?php

// bootstrap the include path
require_once( '../../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Machine.php' );
require_once( 'CTM/Test/Machine/Selector.php' );

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
      $chrome           = $this->getOrPost( 'chrome', '' );
      $chrome_version   = $this->getOrPost( 'chrome_version', '' );
      $firefox          = $this->getOrPost( 'firefox', '' );
      $firefox_version  = $this->getOrPost( 'firefox_version', '' );
      $safari           = $this->getOrPost( 'safari', '' );
      $safari_version   = $this->getOrPost( 'safari_version', '' );

      // see if there is a test machine available for this hostname.
      $test_machine = $this->_findMachineByHostname( $hostname );

      try {
         if ( $test_machine == null ) {

            $new = new CTM_Test_Machine();
            $new->hostname       = $hostname;
            $new->created_at     = time();
            $new->last_modified  = time();
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
         echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
         echo '<ctm_test_machine>' . "\n";
         echo '   <id>' . $test_machine->id . '</id>' . "\n";
         echo '   <hostname>' . $test_machine->hostname . '</hostname>' . "\n";
         echo '   <created_at>' . $test_machine->created_at . '</created_at>' . "\n";
         echo '   <last_modified>' . $test_machine->last_modified . '</last_modified>' . "\n";
         echo '</ctm_test_machine>' . "\n";
         return false;
      }

      echo "invalid request, goodbye.\n";
      return false;

   } 
   
   public function displayBody() {
      return true;
   }

}

$mainPage = new CTM_ET_Phone_Home_Main();
$mainPage->displayPage();
