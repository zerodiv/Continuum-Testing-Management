<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Selector.php' );

class CTM_Site_Test_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );
      $html_source      = $this->getOrPost( 'html_source', '', false );

      if ( $name == '' ) {
         return true;
      }

      $html_source_file = $_FILES['html_source_file']['tmp_name'];

      if ( isset( $html_source_file ) && filesize( $html_source_file) > 0 ) {
         $html_source = file_get_contents( $html_source_file );
      }

      if ( $html_source == '' ) {
         return true;
      }

      $test = null;
      try {
         $sel = new CTM_Test_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) ); 
         $rows = $sel->find( $and_params ); 
         if ( isset( $rows[0] ) ) {
            $test = $rows[0];
         }
      } catch ( Exception $e ) {
      }

      if ( isset( $test ) ) {
         try {
            $test->name = $name;
            $test->description = $description;
            $test->html_source = $html_source;
            $test->modified_at = time();
            $test->modified_by = $_SESSION['user']->id;
            // jeo: TODO: We will need to set this via a drop down eventually.
            // $test->test_status_id = 1; // all tests are created in a pending state.
            $test->save();

            header( 'Location: ' . $this->_baseurl . '/test/folders/?parent_id=' . $test->test_folder_id );
            return false;

         } catch ( Exception $e ) {
            return true;
         }
      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );
      $html_source      = $this->getOrPost( 'html_source', '', false );

      $rows = null;
      try {
         $sel = new CTM_Test_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) ); 
         $rows = $sel->find( $and_params ); 
      } catch ( Exception $e ) {
      }


      $this->printHtml( '<center>' );

      if ( ! isset( $rows[0] ) ) {
         $this->printHtml( '<table class="ctmTable">' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Unable to find test: ' . $this->escapeVariable( $id ) . '</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '</table>' );
      } else {
         $test = $rows[0];
      
         $this->printHtml( '<table>' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td valign="top">' );
         $this->_displayFolderBreadCrumb( $test->test_folder_id );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' ); 
      
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td valign="top">' );
         $this->printHtml( '<table class="ctmTable">' );
         $this->printHtml( '<form enctype="multipart/form-data" method="POST" action="' . $this->_baseurl . '/test/edit/">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Edit Test</th>' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Name:</td>' );
         $this->printHtml( '<td class="odd"><input type="text" name="name" size="30" value="' . $this->escapeVariable( $test->name ) . '"></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2">Description:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $test->description ) . '</textarea></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2">Html Source:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2"><textarea name="html_source" rows="25" cols="60">' . $this->escapeVariable( $test->html_source ) . '</textarea></td>' );
         $this->printHtml( '</tr>' ); 

         if ( $this->isFileUploadAvailable() ) {

            $this->printHtml( '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileUploadSize() . '">' ); 
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="odd">File:</td>' );
            $this->printHtml( '<td class="odd"><input type="file" name="html_source_file"></td>' );
            $this->printHtml( '</tr>' ); 
         
         }

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Save"></center></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '</form>' );
         
         $this->printHtml( '</table>' );
         
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '</table>' );

      }

      $this->printHtml( '</center>' );

      return true;
   }

}

$test_edit_obj = new CTM_Site_Test_Edit();
$test_edit_obj->displayPage();
