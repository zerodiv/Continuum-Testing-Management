<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Selector.php' );
require_once( 'CTM/Test/Html/Source.php' );
require_once( 'CTM/Test/Html/Source/Selector.php' );
require_once( 'CTM/Test/Description.php' );
require_once( 'CTM/Test/Description/Selector.php' );

class CTM_Site_Test_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

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

      $test                = null;
      $html_source_obj     = null;
      $description_obj     = null;
      try {
         $sel = new CTM_Test_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) ); 
         $rows = $sel->find( $and_params ); 
         if ( isset( $rows[0] ) ) {
            $test = $rows[0];
            $rows = null;


            // find the html_source_obj
            $sel = new CTM_Test_Html_Source_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $test->id ) );
            $rows = $sel->find( $and_params );
            if ( isset( $rows[0] ) ) {
               $html_source_obj = $rows[0];
            }
            $rows = null;

            $sel = new CTM_Test_Description_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $test->id ) );
            $rows = $sel->find( $and_params );
            if ( isset( $rows[0] ) ) {
               $description_obj = $rows[0];
            }
            $rows = null;
         }
      } catch ( Exception $e ) {
      }

      if ( isset( $test ) ) {
         try {
            $test->name = $name;
            $test->modified_at = time();
            $test->modified_by = $_SESSION['user']->id;
            // jeo: TODO: We will need to set this via a drop down eventually.
            // $test->test_status_id = 1; // all tests are created in a pending state.
            $test->save();

            // save out the description and html_source
            if ( md5( $description_obj->description ) != md5( $description ) ) {
               // if no change don't save
               $description_obj->description = $description;
               $description_obj->save();
            }

            if ( md5( $html_source_obj->html_source ) != md5( $html_source ) ) {

               $html_source_obj->html_source = $html_source;
               $html_source_obj->save();

               /*
TODO: jeo - need to finish the command parsing and push into the db.
               // Assuming this item parses to be selenium formatted parse it and save it to the db.
               // Remove existing commands (if any)
               $selenium_xhtml = stripslashes( $html_source_obj->html_source ); 
               
               // echo "test:\n";
               // echo $test->html_source . "\n";

               $xml = simplexml_load_string( $selenium_xhtml );

               if ( isset( $xml->body->table->tbody->tr ) ) {
                  $selenium_command_cache = new CTM_Selenium_Command_Cache();

                  foreach ( $xml->body->table->tbody->tr as $tr ) { 
                     list( $command, $target, $value ) = $tr->td;

                     $command_obj = $selenium_command_cache->getByName( $command );

                  }
               }
               */

            }

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

         // lookup the description block
         $description_obj = null;
         try {
            $sel = new CTM_Test_Description_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $id ) );
            $rows = $sel->find( $and_params );
            if ( isset( $rows[0] ) ) {
               $description_obj = $rows[0];
            }
         } catch ( Exception $e ) {
         }
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2">Description:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr>' );
         if ( isset( $description_obj ) ) {
            $this->printHtml( '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $description_obj->description ) . '</textarea></td>' );
         } else {
            $this->printHtml( '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">Failed to find associated description.</textarea></td>' );
         }
         $this->printHtml( '</tr>' );

         $html_source_obj = null;
         try {
            $sel = new CTM_Test_Html_Source_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $id ) );
            $rows = $sel->find( $and_params );
            if ( isset( $rows[0] ) ) {
               $html_source_obj = $rows[0];
            }
         } catch ( Exception $e ) {
         }
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2">Html Source:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr>' );
         if ( isset( $html_source_obj ) ) {
            $this->printHtml( '<td class="odd" colspan="2"><textarea name="html_source" rows="25" cols="60">' . $this->escapeVariable( $html_source_obj->html_source ) . '</textarea></td>' );
         } else {
            $this->printHtml( '<td class="odd" colspan="2"><textarea name="html_source" rows="25" cols="60">Failed to find associated html_source.</textarea></td>' );
         }
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
