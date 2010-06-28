<?php

// bootstrap the include path
require_once( '../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_Main extends CTM_Site {
   public function setupPage() {
      $this->_pagetitle = 'Main';
      return true;
   } 
   
   public function handleRequest() {
      $this->requiresAuth();
      return true;
   } 
   
   public function displayBody() {
      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>Welcome to CTM</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td>Some blurb about how to use the app</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );
   }

}

$mainPage = new CTM_Site_Main();
$mainPage->displayPage();
