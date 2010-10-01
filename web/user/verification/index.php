<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_Verification extends CTM_Site {
   public function setupPage() {
      $this->setPageTitle('Email verification sent!');
      return true;
   } 
   
   public function handleRequest() {
      return true;
   } 
   
   public function displayBody() {
      $this->printHtml( '<div class="aiTableContainer">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>' );
      $this->printHtml( 'We have sent a email to your account please click on the link provided within to verify your email address.' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      return true;
   }
}

$CTM_Verification_Page = new CTM_Site_User_Verification();
$CTM_Verification_Page->displayPage();
