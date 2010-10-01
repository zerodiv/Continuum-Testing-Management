<?php
require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_ForgotPassword_Sent extends CTM_Site {

   public function setupPage() {
      $this->setPageTitle('Forgot Password - Email sent');
      return true;
   }

   public function displayBody() {
      // temp login / registration form.
      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2">Forgot your password email sent to your email address. Please follow the instructions provided.</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$Verify_Page = new CTM_Site_User_ForgotPassword_Sent();
$Verify_Page->displayPage();
