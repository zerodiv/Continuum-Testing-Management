<?php

require_once( '../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_Test_Suite_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'user', 'qa', 'admin' ) );

      $action           = $this->getOrPost( 'action', '' );
      $id               = $this->getOrPost( 'id', '' );

      if ( $action != 'remove' ) {
         return true;
      }

      $rows = null;
      try {
         $test_suite_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Suite_Cache' );

         $test_status_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Status_Cache' );
         $remove_status = $test_status_cache->getByName('deleted');
        

         $suite = $test_suite_cache->getById($id);

         if ( isset( $suite->id ) && isset($remove_status->id) ) {

            $user_obj = $this->getUser();

            $suite->testStatusId = $remove_status->id;
            $suite->save();

            header( 'Location: ' . $this->_baseurl . '/test/suites/?parentId=' . $suite->testFolderId );

         }

      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );

      $test_suite_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Suite_Cache' );
      $test_suite = $test_suite_cache->getById( $id );

      if (isset($test_suite->id)) {
         
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' ); 
         
         $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/suite/remove/">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' ); 
         $this->printHtml( '<input type="hidden" value="remove" name="action">' ); 
         
         $this->printHtml( '<table class="ctmTable aiFullWidth">' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Remove Test Suite</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Folder:</td>' );
         $this->printHtml( '<td>' . $this->_fetchFolderPath( $this->_baseurl . '/tests/', $test_suite->testFolderId ) . '</td>' );
         $this->printHtml( '</tr>' );
        
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="2">Are you sure you would like to remove this test suite?</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Remove"></center></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '</table>' ); 
         $this->printHtml( '</form>' ); 
         $this->printHtml( '</div>' );

      }


      return true;
   }

}

$test_suite_edit_obj = new CTM_Site_Test_Suite_Edit();
$test_suite_edit_obj->displayPage();
