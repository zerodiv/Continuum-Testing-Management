<?php

require_once( 'Light/MVC.php' );

class PFL_Site extends Light_MVC {

   public function displayHeader() {

      // display the normal header.
      parent::displayHeader();

      $this->printHtml( '<ul class="basictab">' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '">' . $this->_sitetitle . '</a></li>' );
      if ( $this->isLoggedIn() ) {
         $this->printHtml( '<li><a href="/suites/">[Test Suites]</a></li>' );
         $this->printHtml( '<li><a href="/user/logout/">[Logout]</a></li>' );
      } else {
         $this->printHtml( '<li><a href="/user/login/">[Login]</a></li>' );
         $this->printHtml( '<li><a href="/user/create/">[Create Account]</a></li>' );
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

}
