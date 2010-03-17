<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_Verification extends CTM_Site {
   public function setupPage() {
      $this->_pagetitle = 'Email verification sent!';
      return true;
   } 
   
   public function handleRequest() {
      return true;
   } 
   
   public function displayBody() {
      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      echo '<th>' . $this->_pagetitle . '</th>';
      echo '</tr>';
      echo '<tr>';
      echo '<td>';
      echo 'We have sent a email to your account please click on the link provided within to verify your email address.';
      echo '</td>';
      echo '</tr>';
      echo '</table>';
      echo '</center>';
      return true;
   }
}

$CTM_Verification_Page = new CTM_Site_User_Verification();
$CTM_Verification_Page->displayPage();
