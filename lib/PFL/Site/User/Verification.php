<?php

class PFL_Site_User_Verification extends Light_MVC {
   public function setupPage() {
      $this->_pagetitle = 'Email verification sent!';
      return true;
   } 
   
   public function handleRequest() {
      return true;
   } 
   
   public function displayBody() {
      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="pflTable">' );
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

