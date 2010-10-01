<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Command/Selector.php' );

class CTM_Site_Test_Edit extends CTM_Site { 
   private $_error_message;

   public function setupPage() {
      $this->_pagetitle = 'Remove Test';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'user', 'qa', 'admin' ) );

      $action           = $this->getOrPost( 'action', '' );
      $id               = $this->getOrPost( 'id', '' );

      $test_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Cache' );
      $test = $test_cache->getById( $id );

      // -- save the cheerleader, save the world --
      if ( $action != 'remove' ) {
         return true;
      }

      // remove the target!
      if ( isset( $test ) && isset( $test->id ) ) {

         $test_status_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Status_Cache' );
         $remove_status = $test_status_cache->getByName('deleted');
        
         if ( isset($remove_status->id) ) {
            $test->testStatusId = $remove_status->id;
            $test->save();
         }

         header( 'Location: ' . $this->_baseurl . '/tests/?parentId=' . $test->testFolderId );
         return false;

      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );

      $test_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Cache' );
      $test = $test_cache->getById( $id );

      if ( ! isset( $test ) ) {
         $this->printHtml( '<div class="aiTableContainer">' );
         $this->printHtml( '<table class="ctmTable">' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Unable to find test: ' . $this->escapeVariable( $id ) . '</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );
      } else {
     
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<form enctype="multipart/form-data" method="POST" action="' . $this->_baseurl . '/test/remove/">' );
         $this->printHtml( '<input type="hidden" value="remove" name="action">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' ); 
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="2">Remove Test</th>' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' );

         if ( isset( $this->_error_message ) ) {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="2"><center><font color="#ff0000">' . $this->_error_message . '</font></td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $test->name ) . '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Folder:</td>' );
         $this->printHtml( '<td>' . $this->_fetchFolderPath( $this->_baseurl . '/tests/', $test->testFolderId ) . '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="2">Are you sure you would like to remove this test?</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="2"><center><input type="submit" value="Remove"></center></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '</table>' );

         $this->printHtml( '</form>' );
        
         $this->printHtml( '</div>' );

      }

      $this->printHtml( '</center>' );

      return true;
   }

}

$test_edit_obj = new CTM_Site_Test_Edit();
$test_edit_obj->displayPage();
