<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );
require_once( 'Light/MVC/Url/Checksum.php' );

class CTM_Site_User_Login extends CTM_Site { 
   private $_urlChecksumObj;

   public function setupPage() {
      $this->setPageTitle('Login');
      $this->_error = '';
      $this->_urlChecksumObj = new Light_MVC_Url_Checksum();
      return true;
   }

   public function handleRequest() {
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
      
      if ( ! isset( $username ) || $username == '' ) {
         return true;
      }
      
      if ( ! isset( $password ) || $password == '' ) {
         return true;
      } 

      // cleanup the login name.
      $username = $this->cleanupUserName( $username );

      try {

         $sel = new CTM_User_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'username', '=', $username )
         );
         
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $user = $rows[0];

            if ( $user->isVerified != 1 ) {
               header( 'Location: ' . $this->getBaseUrl() . '/user/verification/' );
               return false;
            }

            if ( $user->tempPassword != '' && $user->tempPassword == $password ) {
               // $user->tempPassword = '';
               // $user->save();
               header( 'Location: ' . $this->getBaseUrl() . '/user/forgot_password/temp_password/' . $this->_urlChecksumObj->create( array( 'id' => $user->id ) ) );
            }

            $md5_password = md5( $password );

            if ( $md5_password == $user->password ) {
               // found the user auth them in
               $_SESSION['user_id'] = $user->id;
               // they are logged in return them back to the main site.
               header( 'Location: ' . $this->getBaseUrl() );
               return false;
            }

         }

         $this->_error = 'Invalid username or password.';

      } catch ( Exception $e ) {
      }
      return true;
   }

   public function displayBody() {
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
     
      $this->printHtml( '<div class="aiTableContainer">' );
      $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/user/login/">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->getSiteTitle() . ': ' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      if ( $this->_error != '' ) {
         $this->printHtml( '<tr class="even">' );
         $this->printHtml( '<td colspan="2">' . $this->_error . '</td>' );
         $this->printHtml( '</tr>' );
      }
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Email address:</td>' );
      $this->printHtml( '<td><input type="text" size="40" name="username" value="' . $username . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Password:</td>' );
      $this->printHtml( '<td><input type="password" size="40" name="password" value="' . $password . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="2" class="aiButtonRow">' );
      $this->printHtml( '<center>' );
      $this->printHtml( '<input type="submit" value="Login!">' );
      $this->printHtml( '<a href="' . $this->getBaseUrl() . '/user/forgot_password/" class="ctmButton">Forgot my password</a>' );
      $this->printHtml( '</center>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$user_login_page = new CTM_Site_User_Login();
$user_login_page->displayPage();
