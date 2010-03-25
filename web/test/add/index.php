<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Html/Source.php' );
require_once( 'CTM/Test/Description.php' );

class CTM_Site_Test_Add extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {
      $test_folder_id   = $this->getOrPost( 'test_folder_id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );
      $html_source      = $this->getOrPost( 'html_source', '', false );

      if ( $name == '' ) {
         return true;
      }

      $html_source_file = $_FILES['html_source_file']['tmp_name'];

      if ( isset( $html_source_file ) && filesize( $html_source_file ) > 0 ) {
         $html_source = file_get_contents( $html_source_file );
      }

      if ( $html_source == '' ) {
         return true;
      }

      try {

         // create the test.
         $new = new CTM_Test();
         $new->test_folder_id = $test_folder_id;
         $new->name = $name;
         $new->test_status_id = 1; // all tests are created in a pending state.
         $create_at = time(); // yes i know this is paranoia
         $new->created_at = $create_at;
         $new->created_by = $_SESSION['user']->id;
         $new->modified_at = $create_at;
         $new->modified_by = $_SESSION['user']->id;
         $new->save();

         if ( $new->id > 0 ) {

            // add the html source.
            $html_source_obj = new CTM_Test_Html_Source();
            $html_source_obj->test_id     = $new->id;
            $html_source_obj->html_source = $html_source;
            $html_source_obj->save();

            // add the description.
            $test_description_obj = new CTM_Test_Description();
            $test_description_obj->test_id      = $new->id;
            $test_description_obj->description  = $description;
            $test_description_obj->save();

         }
      
         header( 'Location: ' . $this->_baseurl . '/test/folders/?parent_id=' . $test_folder_id );
         return false;

      } catch ( Exception $e ) {
         print_r( $e );
         // failed to insert.
         return true;
      }

      // added our child send us back to our parent
      header( 'Location: ' . $this->_baseurl . '/test/folders/?parent_id=' . $test_folder_id );
      return false;

   }
                           

   public function displayBody() {
      $test_folder_id   = $this->getOrPost( 'test_folder_id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );
      $html_source      = $this->getOrPost( 'html_source', '', false );

      $this->printHtml( '<center>' );

      $this->printHtml( '<table>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );
      $this->_displayFolderBreadCrumb( $test_folder_id );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' ); 
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<form enctype="multipart/form-data" method="POST" action="' . $this->_baseurl . '/test/add/">' );
      $this->printHtml( '<input type="hidden" value="' . $test_folder_id . '" name="test_folder_id">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="4">Add Test</th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Name:</td>' );
      $this->printHtml( '<td class="odd"><input type="text" name="name" size="30" value="' . $this->escapeVariable( $name ) . '"></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2">Description:</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $description ) . '</textarea></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2">Html Source:</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2"><textarea name="html_source" rows="25" cols="60">' . $this->escapeVariable( $html_source ) . '</textarea></td>' );
      $this->printHtml( '</tr>' );

      if ( $this->isFileUploadAvailable() ) {

         $this->printHtml( '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileUploadSize() . '">' );
      
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">File:</td>' );
         $this->printHtml( '<td class="odd"><input type="file" name="html_source_file"></td>' );
         $this->printHtml( '</tr>' );

      }

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Add"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</form>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );

      return true;
   }

}

$test_add_obj = new CTM_Site_Test_Add();
$test_add_obj->displayPage();
