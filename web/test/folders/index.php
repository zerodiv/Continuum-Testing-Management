<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/User/Cache.php' );

class CTM_Site_Test_Folders extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   private function _getParents( $parent_id, &$parents ) {
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $parent_id )
         );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            $parents[] = $rows[0];

            if ( $rows[0]->parent_id > 0 ) {
               $this->_getParents( $rows[0]->parent_id, $parents );
            }

         }
      } catch( Exception $e ) {
         throw $e;
      }
   }

   public function displayBody() {
      $parent_id = $this->getOrPost( 'parent_id', '' );

      if ( $parent_id == '' ) {
         $parent_id = 0;
      }

      $this->printHtml( '<center>' );
      $this->printHtml( '<table>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top" colspan="3">' );
      $this->_displayFolderBreadCrumb( $parent_id );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      // load up the rows for the invidiual folder
      $folder_rows = array();
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'parent_id', '=', $parent_id )
         );
         $folder_rows = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );

      $this->printHtml( '<table class="ctmTable">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="3">Sub Folders</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>Id</th>' );
      $this->printHtml( '<th>Name</th>' );
      $this->printHtml( '<th>Action</th>' );
      $this->printHtml( '</tr>' );

      if ( count( $folder_rows ) == 0 ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="' . $class . '" colspan="3"><center><b>- There are no sub folders defined -</b></td>' );
         $this->printHtml( '</tr>' );
      } else {
         foreach ( $folder_rows as $row ) {
            $class = $this->oddEvenClass();
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="' . $class . '">' . $row->id . '</td>' );
            $this->printHtml( '<td class="' . $class . '"><a href="' . $this->_baseurl . '/test/folders/?parent_id=' . $row->id . '">' . $row->name . '</a></td>' );
            $this->printHtml( '<td class="' . $class . '"><center><a href="' . $this->_baseurl . '/test/folder/edit/?id=' . $row->id . '" class="ctmButton">Edit</a></center></td>' );
            $this->printHtml( '</tr>' );
         }
      }

      $class = $this->oddEvenClass();

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="' . $class . '" colspan="3"><center><a href="' . $this->_baseurl . '/test/folder/add/?parent_id=' . $parent_id . '" class="ctmButton">New Sub Folder</a></center></th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );

      $folder_rows = null; // deallocate the folder_rows - not needed anymore.

      $suite_rows = array();
      try {
         $sel = new CTM_Test_Suite_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'test_folder_id', '=', $parent_id )
         );
         $suite_rows = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<td valign="top">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="5">Test Suites</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>id</th>' );
      $this->printHtml( '<th>Name</th>' );
      $this->printHtml( '<th>Modified At</th>' );
      $this->printHtml( '<th>Modified By</th>' );
      $this->printHtml( '<th>Action</th>' );
      $this->printHtml( '</tr>' );

      if ( count( $suite_rows ) == 0 ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td colspan="5" class="' . $class . '"><center><b>- No Suites defined -</b></center></td>' );
         $this->printHtml( '</tr>' );
      } else {

         $user_cache = new CTM_User_Cache();

         foreach ( $suite_rows as $suite ) {

            $user = $user_cache->getById( $suite->modified_by );

            $class = $this->oddEvenClass();
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="' . $class . '">' . $suite->id . '</td>' );
            $this->printHtml( '<td class="' . $class . '">' . $this->escapeVariable( $suite->name ) . '</td>' );
            $this->printHtml( '<td class="' . $class . '">' . $this->formatDate( $suite->modified_at ) . '</td>' );
            if ( isset( $user ) ) {
               $this->printHtml( '<td class="' . $class . '">' . $this->escapeVariable( $user->email_address ) . '</td>' );
            } else {
               $this->printHtml( '<td class="' . $class . '">Unknown</td>' );
            }
            $this->printHtml( '<td class="' . $class . '"><center><a href="' . $this->_baseurl . '/test/suite/edit/?id=' . $suite->id . '" class="ctmButton">Edit</a></center></td>' );
            $this->printHtml( '</tr>' );
         }

      }

      $class = $this->oddEvenClass();

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="' . $class . '" colspan="5"><center><a href="' . $this->_baseurl . '/test/suite/add/?test_folder_id=' . $parent_id . '" class="ctmButton">New Test Suite</a></center></th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );
    
      $suite_rows = null; // dealloc the suite_rows - not needed anymore.

      $this->printHtml( '<td valign="top">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>Tests</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );


      return true;
   }

}

$test_folders_obj = new CTM_Site_Test_Folders();
$test_folders_obj->displayPage();
