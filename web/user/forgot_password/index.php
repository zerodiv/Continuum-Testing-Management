<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_ForgotPassword extends CTM_Site {

   public function setupPage() {
      $this->setPageTitle('Forgot Password');
      return true;
   } 
   
   public function handleRequest() {
      $username = $this->getOrPost( 'username', null ); 
      
      if ( strlen( $username ) == 0 ) {
         // no username provided
         return true;
      } 
      
      try { 
         $sel = new CTM_User_Selector();
        
         $username = $this->cleanupUsername( $username ) ;

         $and_params = array(
               new Light_Database_Selector_Criteria( 'username', '=', $username ),
         ); 
         
         $rows = $sel->find( $and_params );


         if ( ! isset( $rows[0] ) ) {
            return true;
         }

         // pull the user object off the stack
         $user = $rows[0];

         if ( is_object( $user ) ) {
            // we need to send a email after successfuly updating the db.
            $user->generateTempPassword();
            $user->save();
            $message = '';
            
            $message .= 'Forgot password -  ' . $this->getSiteTitle() . "\n";
            $message .= "\n";
            $message .= " Your username: " . $user->username . "\n";
            $message .= " Your password: " . $user->tempPassword . "\n";
            $message .= "\n";
            $message .= "To login: " . $this->getBaseUrl() . "/user/login/\n";
            $message .= "\n";
            $message .= "Thank you.\n";
            
            $more_headers = '';
            $more_headers .= "From: zerodiv@zerodivide.net\r\n";
            $more_headers .= "Reply-To: zerodiv@zerodivide.net\r\n";
            mail( '<' . $user->username . '>', "Forgot password - " . $this->getSiteTitle(), $message ); 
            
            header( 'Location: ' . $this->getBaseUrl() . '/user/forgot_password/sent/' );
            return false;
         } else {
            $this->_errorMessage = 'Failed to find a user by that username.';
         }
      
      } catch ( Exception $e ) {
         $this->_errorMessage = 'Failed to send you a new password, please try again at a later time.';
         return true;
      } 
      
      return true;
   
   }

   public function displayBody() {
      $username = $this->getOrPost( 'username', null ); 
      
      $this->printHtml( '<center>' );
      $this->printHtml( '<form action="' . $this->getBaseUrl() . '/user/forgot_password/" method="POST">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      
      if ( isset( $this->_errorMessage ) ) {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td colspan="2" class="odd"><center>' . $this->_errorMessage . '</center></td>' );
         $this->printHtml( '</tr>' );
      }


      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="even" colspan="2">Please enter your email address we will send a new password to you.' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">E-mail address:</td>' );
      $this->printHtml( '<td class="even"><input type="text" size="40" name="username" value="' . $username . '"></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2"><center><input type="submit" value="Request new password"></center></td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$Verify_Page = new CTM_Site_User_ForgotPassword();
$Verify_Page->displayPage();
