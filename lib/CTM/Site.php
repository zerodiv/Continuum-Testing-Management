<?php

require_once( 'Light/MVC.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );

// we have to include the user object to thaw it from the session
require_once( 'CTM/User.php' );
require_once( 'CTM/Test/Folder/Cache.php' );

class CTM_Site extends Light_MVC {
   private $_odd_even_class;

   public function displayHeader() {

      // display the normal header.
      parent::displayHeader();

      $user_obj = $this->getUser();
      $role_obj = $user_obj->getRole();

      $this->printHtml( '<div class="aiMainContent clearfix">' );

      $this->printHtml( '<div class="aiTopNav">' );
      $this->printHtml( '<ul class="basictab">' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '">' . $this->_sitetitle . '</a></li>' );
      if ( $this->isLoggedIn() ) {

         $allowed_roles = array( 'qa', 'admin' );
         if ( in_array( $role_obj->name, $allowed_roles ) ) {
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/suites/">Suites</a></li>' );
         }

         $allowed_roles = array( 'user', 'qa', 'admin' );
         if ( in_array( $role_obj->name, $allowed_roles ) ) {
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/tests/">Tests</a></li>' );
         }

         $allowed_roles = array( 'qa', 'admin' );
         if ( in_array( $role_obj->name, $allowed_roles ) ) {
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/param/library/">Parameter Library</a></li>' );
         }

         $allowed_roles = array( 'qa', 'admin' );
         if ( in_array( $role_obj->name, $allowed_roles ) ) {
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/">Runs</a></li>' );
         }


         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/logout/">Logout : ' . $this->escapeVariable( $user_obj->username ) . '</a></li>' );
      } else {
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/login/">Login</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/create/">Create Account</a></li>' );
      }
      $this->printHtml( '</ul>' );

      if ( $this->isLoggedIn() && $role_obj->name == 'admin' ) {
         $this->printHtml( '<ul class="basictab">' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/manager/">Manage Users</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/machines/">Machines</a></li>' );
         $this->printHtml( '</ul>' );
      }

      $this->printHtml( '</div>' );

      return true;
   }

   public function displayFooter() {

      $this->printHtml( '</div>' );

      parent::displayFooter();

      return true;
   }

   public function requiresAuth() {
      if ( $this->isLoggedIn() == true ) {
         return true; 
      } 
      header( 'Location: ' . $this->_baseurl . '/user/login' );
      exit();
   } 

   public function requiresRole( $acceptable_roles ) {
      if ( is_array( $acceptable_roles ) && count( $acceptable_roles ) > 0 ) {
         $user = $this->getUser();
         if ( isset( $user ) ) {
            $current_role = $user->getRole();
            // if their role is in a acceptable role list then we are good for this page.
            if ( isset( $current_role ) && in_array( $current_role->name, $acceptable_roles ) ) {
               return true;
            }
         }
      }
      header( 'Location: ' . $this->_baseurl . '/user/permission/denied/' );
      exit();
   }
   
   public function isLoggedIn() {
      if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] > 0 ) {
         $user_obj = $this->getUser();
         // a user cannot be logged in if they are disabled.
         if ( $user_obj->is_disabled == true ) {
            return false;
         }
         // a user cannot be logged in if they are not verified.
         if ( $user_obj->is_verified != true ) {
            return false;
         }
         return true;
      }
      return false;
   } 

   public function getUser() {
      if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] > 0 ) {
         $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );
         $user_obj = $user_cache->getById( $_SESSION['user_id'] );
         return $user_obj;
      }
      return null;
   }

   public function oddEvenReset() {
      $this->_odd_even_class = null;
   }

   public function oddEvenClass() {
      if ( $this->_odd_even_class == 'odd' ) {
         $this->_odd_even_class = 'even';
      } else if ( $this->_odd_even_class == 'even' ) {
         $this->_odd_even_class = 'odd';
      } else {
         $this->_odd_even_class = 'odd';
      }
      return $this->_odd_even_class;
   }

   public function cleanupUserName( $username ) {
      $username = strtolower( $username );
      $username = ltrim( $username );
      $username = rtrim( $username );
      return $username;
   }

   public function _fetchFolderPath( $current_baseurl, $parent_id ) {
      $folder_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Folder_Cache' );
      $parents = array(); 
      $folder_cache->getFolderParents( $parent_id, $parents );
      $parents = array_reverse( $parents );
      $parents_cnt = count( $parents );
     
      $folder_path = '';
      foreach ( $parents as $parent ) {
         $folder_path .= '/';
         $folder_path .= '<a href="' . $current_baseurl . '?parent_id=' . $parent->id . '">' . $this->escapeVariable( $parent->name ) . '</a>';
      }

      return $folder_path;
   }

   public function _displayFolderBreadCrumb( $current_baseurl, $parent_id = 0 ) {
         $folder_path = $this->_fetchFolderPath( $current_baseurl, $parent_id );

         $folder_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Folder_Cache' );
         $parents = array(); 
         $folder_cache->getFolderParents( $parent_id, $parents );
         $parents_cnt = count( $parents );

         // Look up the chain as needed.
         $children = array();
         if ( $parents_cnt > 0 ) {
            $children = $folder_cache->getFolderChildren( $parents[ ($parents_cnt-1) ]->id );
         }

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Current folder path: ' .  $folder_path . '</td>' );
         if ( count( $children ) > 0 ) {
            $this->printHtml( '<form action="' . $current_baseurl . '" method="POST">' );
            $this->printHtml( '<td><center>' );
            $this->printHtml( 'Switch to Sub Folder: ' );
            $this->printHtml( '<select name="parent_id">' );
            $this->printHtml( '<option value="0">Pick a sub-folder</option>' );
            foreach ( $children as $child ) {
               $this->printHtml( '<option value="' . $child->id . '">' . $this->escapeVariable( $child->name ) . '</option>' );
            }
            $this->printHtml( '</select>' );
            $this->printHtml( '<input type="submit" value="Go!">' );
            $this->printHtml( '&nbsp;<a href="' . $this->_baseurl . '/test/folder/add/?parent_id=' . $parent_id . '" class="ctmButton">New Sub Folder</a>' );
            $this->printHtml( '</center></td>' );
            $this->printHtml( '</form>' );
         } else {
            $this->printHtml( '<td><center>' );
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/folder/add/?parent_id=' . $parent_id . '" class="ctmButton">New Sub Folder</a>' );
            $this->printHtml( '</center></td>' );
         }
         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );

   }

}
