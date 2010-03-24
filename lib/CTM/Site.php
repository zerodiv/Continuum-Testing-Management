<?php

require_once( 'Light/MVC.php' );

// we have to include the user object to thaw it from the session
require_once( 'CTM/User.php' );
require_once( 'CTM/Test/Folder/Selector.php' );

class CTM_Site extends Light_MVC {
   private $_odd_even_class;

   public function displayHeader() {

      // display the normal header.
      parent::displayHeader();

      $this->printHtml( '<ul class="basictab">' );
      $this->printHtml( '<li><a href="' . $this->_baseurl . '">' . $this->_sitetitle . '</a></li>' );
      if ( $this->isLoggedIn() ) {
         $this->printHtml( '<li><a href="/test/folders/">Test Folders</a></li>' );
         $this->printHtml( '<li><a href="/test/machines/">Test Machines</a></li>' );
         $this->printHtml( '<li><a href="/user/logout/">Logout : ' . $this->escapeVariable( $_SESSION['user']->username ) . '</a></li>' );
      } else {
         $this->printHtml( '<li><a href="/user/login/">Login</a></li>' );
         $this->printHtml( '<li><a href="/user/create/">Create Account</a></li>' );
      }
      $this->printHtml( '</ul>' );
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

   private function _getFolderParents( $parent_id, &$parents ) {
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $parent_id ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            $parents[] = $rows[0]; 
            if ( $rows[0]->parent_id > 0 ) {
               $this->_getFolderParents( $rows[0]->parent_id, $parents );
            } 
         }
      } catch( Exception $e ) {
         throw $e;
      }
   }

   public function _displayFolderBreadCrumb( $parent_id = 0 ) {
      // breadcrumb logic goes here.
      if ( $parent_id == 0 ) {
         $this->printHtml( '<ul class="basictab">' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/folders/">Test Folders</a></li>' );
         $this->printHtml( '</ul>' );
      } else {
         // Look up the chain as needed.
         $parents = array(); $this->_getFolderParents( $parent_id, $parents );
         $parents = array_reverse( $parents );
         $this->printHtml( '<ul class="basictab">' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/folders/">Test Folders</a></li>' );
         foreach ( $parents as $parent ) {
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/folders/?parent_id=' . $parent->id . '">' . $parent->name . '</a></li>' );
         }
         $this->printHtml( '</ul>' );
      }
   }

}
