<?php

// bootstrap the include path
require_once( '../bootstrap.php' );
require_once( 'PFL/Site.php' );

class PFL_Site_Main extends PFL_Site {
   public function setupPage() {
      $this->_pagetitle = 'Main';
      return true;
   } 
   
   public function handleRequest() {
      // temp login / registration form.
      if ( $this->isLoggedIn() == true ) {
         // bounce them to test suites.
         header( 'Location: ' . $this->_baseurl . '/test/suites/' );
         return false;
      }
      header( 'Location: ' . $this->_baseurl . '/user/login/' );
      return false;
   } 
   
   public function displayBody() {
      return true;
   }

}

$mainPage = new PFL_Site_Main();
$mainPage->displayPage();
