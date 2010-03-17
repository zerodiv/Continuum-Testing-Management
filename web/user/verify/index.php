<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Factory.php' );

class CTM_Site_User_Verify extends CTM_Site {

   public function setupPage() {
      $this->_pagetitle = 'Verified!';
      return true;
   }
   
   public function handleRequest() {
      $id = $this->getOrPost( 'id', null );
      $checksum = $this->getOrPost( 'checksum', null );
      $verify_checksum = md5( $id . 'jeorem' ); 
      
      if ( $checksum != $verify_checksum ) {
         $this->_pagetitle = 'Failed to verify, invalid checksum';
         return true;
      } 
      
      try {
         $user_factory = new CTM_User_Factory();
         
         list( $v_rv, $v_message ) = $user_factory->verifyUser( $id );
         
         if ( $v_rv == true ) {
            header( 'Location: ' . $this->_baseurl . '/user/login/' );
            return false;
         } else {
            $this->_pagetitle = $v_message;
            return true;
         }
      } catch ( Exception $e ) {
         $this->_pagetitle = 'Failed to setup user session, try again later';
         return true;
      } 
      
      $this->_pagetitle = 'Failed to setup user verification session, try again later.';
      return true;
   } 
   
   public function displayBody() {
      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$CTM_Verify_Page = new CTM_Site_User_Verify();
$CTM_Verify_Page->displayPage();
