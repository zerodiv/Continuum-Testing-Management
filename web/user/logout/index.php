<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_Logout extends CTM_Site { 
   
   public function setupPage() {
      $this->_pagetitle = 'Logout';
      return true;
   } 
   
   public function handleRequest() {
      if ( isset( $_SESSION['user'] ) ) {
         $_SESSION = null;
         session_destroy();
         header( 'Location: ' . $this->_baseurl );
         return false;
      }
      
      header( 'Location: ' . $this->_baseurl . '/user/login/' );
      return false;
   }

}

$CTM_LogoutPage = new CTM_Site_User_Logout();
$CTM_LogoutPage->displayPage();
