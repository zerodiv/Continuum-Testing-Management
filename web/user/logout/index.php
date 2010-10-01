<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_Logout extends CTM_Site { 
   
   public function setupPage() {
      $this->setPageTitle('Logout');
      return true;
   } 
   
   public function handleRequest() {
      if ( isset( $_SESSION['user_id'] ) ) {
         $_SESSION = null;
         session_destroy();
         header( 'Location: ' . $this->getBaseUrl() );
         return false;
      }
      
      header( 'Location: ' . $this->getBaseUrl() . '/user/login/' );
      return false;
   }

}

$CTM_LogoutPage = new CTM_Site_User_Logout();
$CTM_LogoutPage->displayPage();
