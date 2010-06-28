<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

require_once( 'CTM/User/Selector.php' );
require_once( 'CTM/User/Role/Selector.php' );

class CTM_Site_User_Manager_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'User Manager - Edit User';
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
      $user_role_id        = $this->getOrPost( 'user_role_id', null );
      $user_is_disabled    = $this->getOrPost( 'user_is_disabled', null );

      try {
         $sel = new CTM_User_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id )
         );
         $users = $sel->find( $and_params );

         if ( isset( $users[0] ) ) {
            $user = $users[0];

            $user->account_role_id = (int) $user_role_id;
            $user->is_disabled = (int) $user_is_disabled;
            $user->save();

            header( 'Location: ' . $this->_baseurl . '/user/manager/' );
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
      $this->printHtml( '<th colspan="2">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/user/manager/edit/">' );
      $this->printHtml( '<input type="hidden" name="id" value="' . $this->escapeVariable( $id ) . '">' );
      $this->printHtml( '<input type="hidden" name="action" value="save">' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Username:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( $user->username ) . '</td>' );
      $this->printHtml( '</tr>' );
      
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Account Role:</td>' );
      $this->printHtml( '<td><select name="user_role_id">' );
      foreach ( $roles as $role ) {
         if ( $role->id == $user->account_role_id ) {
            $this->printHtml( '<option value="' . $role->id . '" selected>' . $this->escapeVariable( $role->name ) . '</option>' );
         } else {
            $this->printHtml( '<option value="' . $role->id . '">' . $this->escapeVariable( $role->name ) . '</option>' );
         }
      }
      $this->printHtml( '</select></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Email Address:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( $user->email_address ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Is Disabled:</td>' );
      $this->printHtml( '<td><select name="user_is_disabled">' );
      if ( $user->is_disabled == true ) {
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
      if ( $user->is_verified == true ) {
         $this->printHtml( '<td>Yes</td>' );
      } else {
         $this->printHtml( '<td>No</td>' );
      }
      $this->printHtml( '</tr>' );
      
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Verified at:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( date( 'r', $user->verified_when ) ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Created at:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( date( 'r', $user->created_on ) ) . '</td>' );
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
