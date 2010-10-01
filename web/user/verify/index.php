<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_Verify extends CTM_Site {

   public function setupPage() {
      $this->setPageTitle('Verified!');
      return true;
   }
   
   public function handleRequest() {
      $id = $this->getOrPost( 'id', null );
      $checksum = $this->getOrPost( 'checksum', null );
      $verify_checksum = md5( $id . 'jeorem' ); 
      
      if ( $checksum != $verify_checksum ) {
         $this->setPageTitle('Failed to verify, invalid checksum');
         return true;
      } 
      
      try {
         $sel = new CTM_User_Selector();

         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id )
         );

         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $user = $rows[0];
            if ( $user->isVerified == 0 ) {
               // save the verification
               $user->isVerified = 1;
               $user->verifiedWhen = time();
               $user->save();
            }
            // verified
            header( 'Location: ' . $this->getBaseUrl() . '/user/login/' );
            return false;
         }

      } catch ( Exception $e ) {
         $this->setPageTitle('Failed to setup user session, try again later');
         return true;
      }

      $this->setPageTitle('Failed to setup user session, try again later');
      return true;

   } 
   
   public function displayBody() {
      $this->printHtml( '<div class="aiTableContainer">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );
      return true;
   }

}

$CTM_Verify_Page = new CTM_Site_User_Verify();
$CTM_Verify_Page->displayPage();
