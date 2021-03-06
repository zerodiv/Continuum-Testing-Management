<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Suite/Folder/Selector.php' );
require_once( 'CTM/Test/Selector.php' );

class CTM_Site_Test_Suite_Folders extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Suites');
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      $this->requiresRole( array( 'user', 'qa', 'admin' ) );
      return true;
   }

   public function displayBody() {

      $parentId = $this->getOrPost( 'parentId', '' );

      $user_obj = $this->getUser();
      $role_obj = $user_obj->getRole();

      if ( $parentId == '' ) {
         $parentId = 1;
      }

      /*
      if ( $role_obj->name == 'user' ) {
         $user_folder = $this->getUserFolder();
         $parentId = $user_folder->id;
      }
      */

      // need these caches for this page to hum.
      $test_status_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Status_Cache' );
      $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );

      $deleted_status = $test_status_cache->getByName( 'deleted' );

      $this->oddEvenReset();

      $suite_rows = array();
      try {
         $sel = new CTM_Test_Suite_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'testSuiteFolderId', '=', $parentId ),
               new Light_Database_Selector_Criteria( 'testStatusId', '!=', $deleted_status->id )
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
      $this->_displayFolderBreadCrumb( $this->getBaseUrl() . '/test/suites/', $parentId, true );
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

            $user = $user_cache->getById( $suite->modifiedBy );
            $suite_status = $test_status_cache->getById( $suite->testStatusId );

            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td>' . $this->escapeVariable( $suite->name ) . '</td>' );
            if ( isset( $suite_status ) ) {
               $this->printHtml( '<td>' . $suite_status->name . '</td>' );
            } else {
               $this->printHtml( '<td>' . $suite->testStatusId . '</td>' );
            }
            $this->printHtml( '<td>' . $this->formatDate( $suite->modifiedAt ) . '</td>' );
            if ( isset( $user ) ) {
               $this->printHtml(
                     '<td><a href="mailto:' . $this->escapeVariable( $user->emailAddress ) . '">' .
                     $this->escapeVariable($user->emailAddress) . 
                     '</a></td>'
               );
            } else {
               $this->printHtml( '<td>Unknown</td>' );
            }
            $this->printHtml( '<td><center>' );
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/suite/edit/?id=' . $suite->id . '" class="ctmButton">Edit</a>' );
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/suite/plan/?id=' . $suite->id . '" class="ctmButton">Edit Plan</a>' );
            /*
            if ( $suite->revisionCount > 1 ) {
               $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/suite/revisions/?id=' . $test->id . '" class="ctmButton">Revisions</a>' );
            }
            */
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/suite/remove/?id=' . $suite->id . '" class="ctmButton">Remove</a>' );
            $this->printHtml( '</center></td>' );
            $this->printHtml( '</tr>' );
         }

      }

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="aiButtonRow" colspan="6"><center><a href="' . $this->getBaseUrl() . '/test/suite/add/?testSuiteFolderId=' . $parentId . '" class="ctmButton">New Test Suite</a></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );
    
      $suite_rows = null; // dealloc the suite_rows - not needed anymore.
      $this->oddEvenReset();

      return true;
   }

}

$test_folders_obj = new CTM_Site_Test_Suite_Folders();
$test_folders_obj->displayPage();
