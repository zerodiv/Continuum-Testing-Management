<?php

require_once( '../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Selector.php' );

class CTM_Site_Test_Folders extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Tests';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      return true;

   }

   public function displayBody() {
      $parent_id = $this->getOrPost( 'parent_id', '' );

      if ( $parent_id == '' ) {
         $parent_id = 1;
      }

      // need these caches for this page to hum.
      $test_status_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Status_Cache' );
      $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );

      $this->oddEvenReset();

      $test_rows = array();
      try {
         $sel = new CTM_Test_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'test_folder_id', '=', $parent_id )
         );
         $test_rows = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="5">Tests</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="5">' );
      $this->_displayFolderBreadCrumb( $this->_baseurl . '/tests/', $parent_id );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td>Name</td>' );
      $this->printHtml( '<td>Test Status</td>' );
      $this->printHtml( '<td>Modified At</td>' );
      $this->printHtml( '<td>Modified By</td>' );
      $this->printHtml( '<td>Action</td>' );
      $this->printHtml( '</tr>' );

      if ( count( $test_rows ) == 0 ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr class="' . $class . '">' );
         $this->printHtml( '<td colspan="5"><center><b>- No tests defined -</b></center></td>' );
         $this->printHtml( '</tr>' );
      } else {

         foreach ( $test_rows as $test ) {
            $class = $this->oddEvenClass();

            $user = $user_cache->getById( $test->modified_by );
            $test_status = $test_status_cache->getById( $test->test_status_id );
            
            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td>' . $this->escapeVariable( $test->name ) . '</td>' );
            if ( isset( $test_status ) ) {
               $this->printHtml( '<td>' . $test_status->name . '</td>' );
            } else {
               $this->printHtml( '<td>' . $test->test_status_id . '</td>' );
            }
            $this->printHtml( '<td>' . $this->formatDate( $test->modified_at ) . '</td>' );
            if ( isset( $user ) ) {
               $this->printHtml( '<td>' . $this->escapeVariable( $user->email_address ) . '</td>' );
            } else {
               $this->printHtml( '<td>Unknown</td>' );
            }
            $this->printHtml( '<td><center>' );
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/edit/?id=' . $test->id . '" class="ctmButton">Edit</a>' );
            if ( $test->revision_count > 1 ) {
               $this->printHtml( '<a href="' . $this->_baseurl . '/test/revisions/?id=' . $test->id . '" class="ctmButton">Revisions</a>' );
            }
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/download/?id=' . $test->id . '" class="ctmButton" target="_new">Download</a>' );
            $this->printHtml( '</center></td>' );
            $this->printHtml( '</tr>' );

         }
      }

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="aiButtonRow" colspan="6"><center><a href="' . $this->_baseurl . '/test/add/?test_folder_id=' . $parent_id . '" class="ctmButton">New Test</a></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      $test_rows = null; // dealloc the test rows - not needed anymore
      $this->oddEvenReset();

      return true;
   }

}

$test_folders_obj = new CTM_Site_Test_Folders();
$test_folders_obj->displayPage();