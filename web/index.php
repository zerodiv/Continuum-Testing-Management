<?php

// bootstrap the include path
require_once( '../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_Main extends CTM_Site {
   public function setupPage() {
      $this->_pagetitle = 'Main';
      return true;
   } 
   
   public function handleRequest() {
      // temp login / registration form.
      if ( $this->isLoggedIn() == true ) {
         // bounce them to test suites.
         header( 'Location: ' . $this->_baseurl . '/test/folders/' );
         return false;
      }
      header( 'Location: ' . $this->_baseurl . '/user/login/' );
      return false;
   } 
   
   public function displayBody() {
      return true;
   }

}

$mainPage = new CTM_Site_Main();
$mainPage->displayPage();
