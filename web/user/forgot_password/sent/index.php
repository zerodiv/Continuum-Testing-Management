<?php

class PFL_Site_User_ForgotPassword_Sent extends PFL_Site {

   public function setupPage() {
      $this->_pagetitle = 'Forgot Password - Email sent';
      return true;
   }

   public function displayBody() {
      // temp login / registration form.
      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="pflTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2">Forgot your password email sent to your email address. Please follow the instructions provided.</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$Verify_Page = new PFL_Site_User_ForgotPassword_Sent();
$Verify_Page->displayPage();
