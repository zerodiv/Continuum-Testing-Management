<?php

require_once( 'Light/MVC.php' );

// we have to include the user object to thaw it from the session
require_once( 'CTM/User.php' );

class CTM_Site extends Light_MVC {
   private $_odd_even_class;

   public function displayHeader() {

      // display the normal header.
      parent::displayHeader();

      $this->printHtml( '<ul class="basictab">' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '">' . $this->_sitetitle . '</a></li>' );
      if ( $this->isLoggedIn() ) {
         $this->printHtml( '<li><a href="/test/folders/">Test Folders</a></li>' );
         $this->printHtml( '<li><a href="/test/suites/">Test Suites</a></li>' );
         $this->printHtml( '<li><a href="/user/logout/">Logout</a></li>' );
      } else {
         $this->printHtml( '<li><a href="/user/login/">Login</a></li>' );
         $this->printHtml( '<li><a href="/user/create/">Create Account</a></li>' );
      }
      $this->printHtml( '</ul>' );
      return true;
   }

   public function requiresAuth() {
      if ( $this->isLoggedIn() != true ) {
         header( 'Location: ' . $this->_baseurl . '/user/login' );
         exit();
      } 
      return true; 
   } 
   
   public function isLoggedIn() {
      if ( isset( $_SESSION['user'] ) && $_SESSION['user']->id > 0 ) {
         return true;
      }
      return false;
   } 

   public function oddEvenClass() {
      if ( $this->_odd_even_class == 'odd' ) {
         $this->_odd_even_class = 'even';
      } else if ( $this->_odd_even_class == 'even' ) {
         $this->_odd_even_class = 'odd';
      } else {
         $this->_odd_even_class = 'odd';
      }
      return $this->_odd_even_class;
   }

}
