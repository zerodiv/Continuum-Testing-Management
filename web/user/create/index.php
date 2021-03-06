<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Config.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_Create extends CTM_Site {
   private $_displayError;

   public function setupPage() {
      $this->setPageTitle('Create User');
      $this->_displayError = '';
      return true;
   }

   public function handleRequest() {

      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );

      if ( $username == '' ) {
         $this->_displayError = 'Please provide a username for this account.';
         return true;
      }

      if ( $password == '' ) {
         $this->_displayError = 'Please provide a password for this account.';
         return true;
      }

      try {

         $emailAddress = $username;

         $username = $this->cleanupUsername( $username );

         $user_sel = new CTM_User_Selector();

         $and_params = array(
               new Light_Database_Selector_Criteria( 'username', '=', $username ),
         ); 
         
         $rows = $user_sel->find( $and_params );

         if ( count( $rows ) > 0 ) {
            $this->_displayError = 'There is already a user by that username in the system.';
            return true;
         }

         // create the new user.
         $user = new CTM_User();
         $user->ctmUserRoleId = 1; // Default login role. 
         $user->username = $username;
         $user->password = md5( $password );
         $user->emailAddress = $emailAddress;
         $user->isDisabled = 0;
         $user->isVerified = 0;
         $user->verifiedWhen = 0;
         $user->createdOn = time();
         $user->tempPassword = '';
         $user->save();

         if ( $user->id > 0 ) {
            // found the user hooray.

            $verify_sign = md5( $user->id . 'jeorem' );

            $verify_url = $this->getBaseUrl() . '/user/verify/?id=' . $user->id . '&checksum=' . $verify_sign;

            $message = '';
            $message .= 'Welcome to ' . $this->getSiteTitle() . "\n";
            $message .= "\n";
            $message .= " Your username is: " . $username . "\n";
            $message .= "\n";
            $message .= "Please click on this verification link to prove you have a valid email address:\n";
            $message .= "   " . $verify_url . "\n";
            $message .= "\n";
            $message .= "Thank you.\n";

            $more_headers = '';
            $more_headers .= "From: " . Light_Config::get( 'CTM_Site_Config', 'CREATE_EMAIL_FROM' ) . "\r\n";
            $more_headers .= "Reply-To: " . Light_Config::get( 'CTM_Site_Config', 'CREATE_EMAIL_FROM' ) . "\r\n";

            mail( '<' . $username . '>', "Welcome to " . $this->getSiteTitle(), $message );

            header( 'Location: ' . $this->getBaseUrl() . '/user/verification/' );
            return true;
         }

         $this->_displayError = 'Failed to create user account at this time.';
         return true;

      } catch ( Exception $e ) {
         $this->_displayError = 'Failed to create user account at this time.';
      }

      return true;

   }


   public function displayBody() {
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
      $this->printHtml( '<div class="aiTableContainer">' );
      $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/user/create/">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->getSiteTitle() . ': ' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      if ( isset( $this->_displayError ) ) {
         $this->printHtml( '<tr class="even">' );
         $this->printHtml( '<td colspan="2">' . $this->_displayError . '</td>' );
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
      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="2"><center>' );
      $this->printHtml( '<input type="submit" value="Create User!">' );
      $this->printHtml( '</center></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</div>' );
      return true;
   }

}

$CreateUser_Page = new CTM_Site_User_Create();
$CreateUser_Page->displayPage();
