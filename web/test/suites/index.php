<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Selector.php' );

class CTM_Site_Test_Folders extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Suites';
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      $this->requiresRole( array( 'qa', 'admin' ) );
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

      $suite_rows = array();
      try {
         $sel = new CTM_Test_Suite_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'test_folder_id', '=', $parent_id )
         );
         $suite_rows = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="5">Test Suites</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="5">' );
      $this->_displayFolderBreadCrumb( $this->_baseurl . '/test/suites/', $parent_id );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td>Name</td>' );
      $this->printHtml( '<td>Status</td>' );
      $this->printHtml( '<td>Modified At</td>' );
      $this->printHtml( '<td>Modified By</td>' );
      $this->printHtml( '<td>Action</td>' );
      $this->printHtml( '</tr>' );

      if ( count( $suite_rows ) == 0 ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr class="' . $class . '">' );
         $this->printHtml( '<td colspan="5"><center><b>- No suites defined -</b></center></td>' );
         $this->printHtml( '</tr>' );
      } else {


         foreach ( $suite_rows as $suite ) {

            $class = $this->oddEvenClass();

            $user = $user_cache->getById( $suite->modified_by );
            $suite_status = $test_status_cache->getById( $suite->test_status_id );

            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td>' . $this->escapeVariable( $suite->name ) . '</td>' );
            if ( isset( $suite_status ) ) {
               $this->printHtml( '<td>' . $suite_status->name . '</td>' );
            } else {
               $this->printHtml( '<td>' . $suite->test_status_id . '</td>' );
            }
            $this->printHtml( '<td>' . $this->formatDate( $suite->modified_at ) . '</td>' );
            if ( isset( $user ) ) {
               $this->printHtml( '<td>' . $this->escapeVariable( $user->email_address ) . '</td>' );
            } else {
               $this->printHtml( '<td>Unknown</td>' );
            }
            $this->printHtml( '<td><center>' );
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/suite/edit/?id=' . $suite->id . '" class="ctmButton">Edit</a>' );
            $this->printHtml( '<a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $suite->id . '" class="ctmButton">Edit Plan</a>' );
            /*
            if ( $suite->revision_count > 1 ) {
               $this->printHtml( '<a href="' . $this->_baseurl . '/test/suite/revisions/?id=' . $test->id . '" class="ctmButton">Revisions</a>' );
            }
            */
            $this->printHtml( '</center></td>' );
            $this->printHtml( '</tr>' );
         }

      }

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="aiButtonRow" colspan="6"><center><a href="' . $this->_baseurl . '/test/suite/add/?test_folder_id=' . $parent_id . '" class="ctmButton">New Test Suite</a></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );
    
      $suite_rows = null; // dealloc the suite_rows - not needed anymore.
      $this->oddEvenReset();

      return true;
   }

}

$test_folders_obj = new CTM_Site_Test_Folders();
$test_folders_obj->displayPage();
