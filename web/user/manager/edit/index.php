<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

require_once( 'CTM/User/Selector.php' );
require_once( 'CTM/User/Role/Selector.php' );

class CTM_Site_User_Manager_Edit extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('User Manager - Edit User');
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      $this->requiresRole( array( 'admin' ) );

      $action              = $this->getOrPost( 'action', null );

      if ( $action != 'save' ) {
         return true;
      }

      $id                  = $this->getOrPost( 'id', null );
      $ctmUserRoleId       = $this->getOrPost( 'ctmUserRoleId', null );
      $userIsDisabled    = $this->getOrPost( 'userIsDisabled', null );

      try {
         $sel = new CTM_User_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id )
         );
         $users = $sel->find( $and_params );

         if ( isset( $users[0] ) ) {
            $user = $users[0];

            $user->ctmUserRoleId = (int) $ctmUserRoleId;
            $user->isDisabled = (int) $userIsDisabled;
            $user->save();

            header( 'Location: ' . $this->getBaseUrl() . '/user/manager/' );
            return false;

         }

      } catch ( Exception $e ) {
      }

      return true;
   }

   public function displayBody() {
      $id = $this->getOrPost( 'id', null );
      $user = null;
      $roles = array();
      try {
         $sel = new CTM_User_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id )
         );
         $users = $sel->find( $and_params );
         if ( isset( $users[0] ) ) {
            $user = $users[0];
         }
         $sel = new CTM_User_Role_Selector();
         $roles = $sel->find( array(), array(), array( 'id' ) );
      } catch ( Exception $e ) {
      }

      if ( empty( $user ) ) {
         return false;
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_sitetitle . ': ' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/user/manager/edit/">' );
      $this->printHtml( '<input type="hidden" name="id" value="' . $this->escapeVariable( $id ) . '">' );
      $this->printHtml( '<input type="hidden" name="action" value="save">' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Username:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( $user->username ) . '</td>' );
      $this->printHtml( '</tr>' );
      
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Account Role:</td>' );
      $this->printHtml( '<td><select name="ctmUserRoleId">' );
      foreach ( $roles as $role ) {
         if ( $role->id == $user->ctmUserRoleId ) {
            $this->printHtml( '<option value="' . $role->id . '" selected>' . $this->escapeVariable( $role->name ) . '</option>' );
         } else {
            $this->printHtml( '<option value="' . $role->id . '">' . $this->escapeVariable( $role->name ) . '</option>' );
         }
      }
      $this->printHtml( '</select></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Email Address:</td>' );
      $this->printHtml(
            '<td><a href="mailto:' . $this->escapeVariable($user->emailAddress) . '">' .
            $this->escapeVariable($user->emailAddress) .
            '</td>'
      );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Is Disabled:</td>' );
      $this->printHtml( '<td><select name="userIsDisabled">' );
      if ( $user->isDisabled == true ) {
         $this->printHtml( '<option value="1" selected>Yes</option>' );
         $this->printHtml( '<option value="0">No</option>' );
      } else {
         $this->printHtml( '<option value="1">Yes</option>' );
         $this->printHtml( '<option value="0" selected>No</option>' );
      }
      $this->printHtml( '</select></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Is Verified:</td>' );
      if ( $user->isVerified == true ) {
         $this->printHtml( '<td>Yes</td>' );
      } else {
         $this->printHtml( '<td>No</td>' );
      }
      $this->printHtml( '</tr>' );
      
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Verified at:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( date( 'r', $user->verifiedWhen ) ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Created at:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( date( 'r', $user->createdOn ) ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2"><center><input type="submit" value="Save"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</form>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$page_obj = new CTM_Site_User_Manager_Edit();
$page_obj->displayPage();
