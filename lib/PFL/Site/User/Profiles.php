<?php

class PFL_Site_User_Profiles extends Light_MVC {
   public function setupPage() {
      $this->_pagetitle = 'My Profiles';
      return true;
   }
   public function handleRequest() {
      // this page requires auth.
      $this->requiresAuth();
      return true;
   }
   public function displayBody() {
      
      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="pflTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );

      return true;
   }

}
