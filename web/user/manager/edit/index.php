<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_Manager_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'User Manager - Edit User';
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      return true;
   }

   public function displayBody() {
      $id = $this->getOrPost( 'id', null );
      $user = null;
      try {
         $sel = new CTM_User_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id )
         );
         $users = $sel->find();
         if ( isset( $users[0] ) ) {
            $user = $users[0];
         }
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$page_obj = new CTM_Site_User_Manager_Edit();
$page_obj->displayPage();
