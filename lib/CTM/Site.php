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

      $this->printHtml( '<div class="aiMainContent clearfix">' );

      $this->printHtml( '<div class="aiTopNav">' );
      $this->printHtml( '<ul class="basictab">' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '">' . $this->_sitetitle . '</a></li>' );
      if ( $this->isLoggedIn() ) {
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/folders/">Test Folders</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/param/library/">Test Parameter Library</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/runs/">Test Runs</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/machines/">Test Machines</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/logout/">Logout : ' . $this->escapeVariable( $_SESSION['user']->username ) . '</a></li>' );
      } else {
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/login/">Login</a></li>' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/create/">Create Account</a></li>' );
      }
      $this->printHtml( '</ul>' );

      if ( $this->isLoggedIn() ) {
         $this->printHtml( '<ul class="basictab">' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/user/manager/">Manage Users</a></li>' );
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
      if ( $this->isLoggedIn() != true ) {
         header( 'Location: ' . $this->_baseurl . '/user/login' );
         exit();
      } 
      return true; 
   } 
   
   public function isLoggedIn() {
      if ( isset( $_SESSION['user'] ) && $_SESSION['user']->id > 0 ) {
         return true;
      }
      return false;
   } 

   public function getUser() {
      if ( isset( $_SESSION['user'] ) && $_SESSION['user']->id > 0 ) {
         return $_SESSION['user'];
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

   public function _displayFolderBreadCrumb( $parent_id = 0 ) {
         // Look up the chain as needed.
         $folder_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Folder_Cache' );
         $parents = array(); 
         $folder_cache->getFolderParents( $parent_id, $parents );
         // $this->_getFolderParents( $parent_id, $parents );
         $parents = array_reverse( $parents );
         $parents_cnt = count( $parents );

         $children = array();
         if ( $parents_cnt > 0 ) {
            $children = $folder_cache->getFolderChildren( $parents[ ($parents_cnt-1) ]->id );
         }

         $folder_path = '';
         $current_parent = 0;
         foreach ( $parents as $parent ) {
            $current_parent++;
            $folder_path .= '/';
            $folder_path .= '<a href="' . $this->_baseurl . '/test/folders/?parent_id=' . $parent->id . '">' . $parent->name . '</a>';
         }
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Current folder path: ' .  $folder_path . '</td>' );
         if ( count( $children ) > 0 ) {
            $this->printHtml( '<form action="' . $this->_baseurl . '/test/folders/" method="POST">' );
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
