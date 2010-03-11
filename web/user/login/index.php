<?php

require_once( '../../../bootstrap.php' );
require_once( 'PFL/Site.php' );
require_once( 'PFL/User/Factory.php' );

class PFL_Site_User_Login extends PFL_Site { 

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
      
      try {
         $user_factory_obj = new PFL_User_Factory();
         
         list( $login_rv, $user ) = $user_factory_obj->loginUser( $username, $password );
         
         if ( $login_rv == true ) {
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
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
      
      $this->printHtml( '<center>' );
      $this->printHtml( '<br/><br/>' );
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/user/login/">' );
      $this->printHtml( '<table class="pflTable">' );
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
      $this->printHtml( '<center><input type="submit" value="Login!"></center>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="2" class="even">' );
      $this->printHtml( '<center><a href="/user/create/">[Create a new login]</a>' );
      $this->printHtml( '<a href="/user/forgot_password/">[Forgot my password]</a>' );
      $this->printHtml( '</center></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$user_login_page = new PFL_Site_User_Login();
$user_login_page->displayPage();
