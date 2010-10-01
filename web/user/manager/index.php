<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/User/Selector.php' );

class CTM_Site_User_Manager extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'User Manager';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'admin' ) );

      return true;

   }

   public function displayBody() {
      $users = null;
      try {
         $sel = new CTM_User_Selector();
         $users = $sel->find();
      } catch ( Exception $e ) {
      }
      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="6">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td>Id:</td>' );
      $this->printHtml( '<td>Username:</td>' );
      $this->printHtml( '<td>Email Address:</td>' );
      $this->printHtml( '<td>Role:</td>' );
      $this->printHtml( '<td>Is Disabled:</td>' );
      $this->printHtml( '<td>Actions:</td>' );
      $this->printHtml( '</tr>' );

      foreach ( $users as $user ) {
         $role = $user->getRole();
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr class="' . $class . '">' );
         $this->printHtml( '<td>' . $this->escapeVariable( $user->id ) . '</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $user->username ) . '</td>' );
         $this->printHtml(
               '<td><a href="mailto:' . $this->escapeVariable($user->emailAddress) . '">' .
               $this->escapeVariable($user->emailAddress) .
               '</td>' );
         $this->printHtml( '<td><center>' . $this->escapeVariable( $role->name ) . '</center></td>' );
         if ( $user->isDisabled == true ) {
            $this->printHtml( '<td><center>Yes</center></td>' );
         } else {
            $this->printHtml( '<td><center>No</center></td>' );
         }
         $this->printHtml( '<td><center>' );
         $this->printHtml( '<a href="' . $this->_baseurl . '/user/manager/edit/?id=' . $user->id . '" class="ctmButton">Edit</a>' );
         $this->printHtml( '</center></td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$page_obj = new CTM_Site_User_Manager();
$page_obj->displayPage();
