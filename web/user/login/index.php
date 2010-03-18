<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_Login extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Login';
      return true;
   }

   public function handleRequest() {
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
               new Light_Database_Selector_Criteria( 'password', '=', md5( $password ) ),
         );
         
         $rows = $sel->find( $and_params );

         // print_r( $rows );

         if ( isset( $rows[0] ) ) {
            // found the user auth them in
            $_SESSION['user'] = $rows[0];
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
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
      
      $this->printHtml( '<center>' );
      $this->printHtml( '<br/><br/>' );
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/user/login/">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Email address:</td>' );
      $this->printHtml( '<td class="even"><input type="text" size="40" name="username" value="' . $username . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Password:</td>' );
      $this->printHtml( '<td class="even"><input type="password" size="40" name="password" value="' . $password . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="2" class="odd">' );
      $this->printHtml( '<center>' );
      $this->printHtml( '<input type="submit" value="Login!">' );
      $this->printHtml( '<a href="/user/forgot_password/" class="ctmButton">Forgot my password</a>' );
      $this->printHtml( '</center>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$user_login_page = new CTM_Site_User_Login();
$user_login_page->displayPage();
