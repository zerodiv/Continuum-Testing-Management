<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_Manager extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'User Manager';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      return true;
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
      
      if ( ! isset( $username ) ) {
         return true;
      }
      
      if ( ! isset( $password ) ) {
         return true;
      } 

      // cleanup the login name.
      $username = $this->cleanupUserName( $username );

      try {
         $sel = new CTM_User_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'username', '=', $username ),
               new Light_Database_Selector_Criteria( 'password', '=', md5( $password ) )
         );
         
         $rows = $sel->find( $and_params );

         // print_r( $rows );

         if ( isset( $rows[0] ) ) {
            $user = $rows[0];
            if ( $user->is_verified != 1 ) {
               header( 'Location: ' . $this->_baseurl . '/user/verification/' );
               return false;
            }
            // found the user auth them in
            $_SESSION['user'] = $user;
            // they are logged in return them back to the main site.
            header( 'Location: ' . $this->_baseurl );
            return false;
         }

      } catch ( Exception $e ) {
      }
      return true;
   }

   public function displayBody() {
      // temp login / registration form.
      $users = null;
      try {
         $sel = new CTM_User_Selector();
         $users = $sel->find();
      } catch ( Exception $e ) {
      }
      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="5">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td>Id:</td>' );
      $this->printHtml( '<td>Username:</td>' );
      $this->printHtml( '<td>Email Address:</td>' );
      $this->printHtml( '<td>Is Disabled:</td>' );
      $this->printHtml( '<td>Actions:</td>' );
      $this->printHtml( '</tr>' );

      foreach ( $users as $user ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr class="' . $class . '">' );
         $this->printHtml( '<td>' . $this->escapeVariable( $user->id ) . '</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $user->username ) . '</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $user->email_address ) . '</td>' );
         if ( $user->is_disabled == true ) {
            $this->printHtml( '<td>Yes</td>' );
         } else {
            $this->printHtml( '<td>No</td>' );
         }
         $this->printHtml( '<td></td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$page_obj = new CTM_Site_User_Manager();
$page_obj->displayPage();
