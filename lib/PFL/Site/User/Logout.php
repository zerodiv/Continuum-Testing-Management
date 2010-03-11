<?php

class PFL_Site_User_Logout extends Light_MVC { 
   
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
