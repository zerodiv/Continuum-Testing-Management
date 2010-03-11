<?php

class PFL_Site_User_Create extends PFL_Site {
   private $_displayError;

   public function setupPage() {
      $this->_pagetitle = 'Create User';
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

         $user_factory = new PFL_User_Factory();
         list( $user_rv, $user_message ) = $user_factory->createUser( $username, $password );

         if ( $user_rv == true ) {

            $verify_sign = md5( $user_message->id . 'jeorem' );

            $verify_url = $this->_baseurl . '/user/verify/?id=' . $user_message->id . '&checksum=' . $verify_sign;

            $message = '';
            $message .= 'Welcome to ' . $this->_sitetitle . "\n";
            $message .= "\n";
            $message .= " Your username is: " . $username . "\n";
            $message .= "\n";
            $message .= "Please click on this verification link to prove you have a valid email address:\n";
            $message .= "   " . $verify_url . "\n";
            $message .= "\n";
            $message .= "Thank you.\n";

            $more_headers = '';
            $more_headers .= "From: " . PFL_Site_User_Create_Config::CREATE_EMAIL_FROM() . "\r\n";
            $more_headers .= "Reply-To: " . PFL_Site_User_Create_Config::CREATE_EMAIL_FROM() . "\r\n";

            mail( '<' . $username . '>', "Welcome to " . $this->_sitetitle, $message );

            header( 'Location: ' . $this->_baseurl . '/user/verification/' );
            return true;
         }

         $this->_displayError = $user_message;
         return true;

      } catch ( Exception $e ) {
         $this->_displayError = 'Failed to create user account at this time.';
      }

      return true;

   }


   public function displayBody() {
      $username = $this->getOrPost( 'username', '' );
      $password = $this->getOrPost( 'password', '' );
      $this->printHtml( '<center>' );
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/user/create/">' );
      $this->printHtml( '<table class="pflTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );
      if ( isset( $this->_displayError ) ) {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="even" colspan="2">' . $this->_displayError . '</td>' );
         $this->printHtml( '</tr>' );
      }
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Email address:</td>' );
      $this->printHtml( '<td class="even"><input type="text" size="40" name="username" value="' . $username . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Password:</td>' );
      $this->printHtml( '<td class="even"><input type="password" size="40" name="password" value="' . $password . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="2" class="even"><center>' );
      $this->printHtml( '<input type="submit" value="Create User!">' );
      $this->printHtml( '</center></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      return true;
   }

}

$CreateUser_Page = new PFL_Site_User_Create();
$CreateUser_Page->displayPage();
