<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Selector.php' );
require_once( 'CTM/Test/Command/Selector.php' );

class CTM_Site_Test_Edit extends CTM_Site { 
   private $_error_message;

   public function setupPage() {
      $this->_pagetitle = 'Edit Test';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      $action           = $this->getOrPost( 'action', '' );
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $baseurl          = $this->getOrPost( 'baseurl', '' );
      $description      = $this->getOrPost( 'description', '' );

      if ( $action != 'save' ) {
         return true;
      }

      if ( $name == '' ) {
         $this->_error_message = 'Test Name is a required field';
         return true;
      }

      if ( $baseurl == '' ) {
         $this->_error_message = 'Base URL is a required field';
         return true;
      }

      $had_file = false;

      $html_source = '';

      $html_source_file = $_FILES['html_source_file']['tmp_name'];

      if ( isset( $html_source_file ) && filesize( $html_source_file) > 0 ) {
         $html_source = file_get_contents( $html_source_file );
         $had_file = true;
      }

      $test                = null;
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

            $user_obj = $this->getUser();
            $test->name = $name;
            $test->modified_at = time();
            $test->modified_by = $user_obj->id;
            $test->revision_count = $test->revision_count + 1;
            // jeo: TODO: We will need to set this via a drop down eventually.
            // $test->test_status_id = 1; // all tests are created in a pending state.
            $test->save();

            $test->setDescription( $description );

            if ( $had_file == true ) {
               $test->setHtmlSource( $user_obj, $html_source );
            }

            $baseurl_obj = $test->getBaseUrl();

            if ( isset( $baseurl_obj ) && $baseurl_obj->baseurl != $baseurl ) {
               $baseurl_obj->baseurl = $baseurl;
               $baseurl_obj->save();
            } else {
               $test->setBaseUrl( $baseurl );
            }

            // save the revision information.
            $test->saveRevision();

            header( 'Location: ' . $this->_baseurl . '/test/folders/?parent_id=' . $test->test_folder_id );
            return false;

         } catch ( Exception $e ) {
            print_r( $e );
            return true;
         }
      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      $rows = null;
      try {
         $sel = new CTM_Test_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) ); 
         $rows = $sel->find( $and_params ); 
      } catch ( Exception $e ) {
      }


      if ( ! isset( $rows[0] ) ) {
         $this->printHtml( '<div class="aiTableContainer">' );
         $this->printHtml( '<table class="ctmTable">' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Unable to find test: ' . $this->escapeVariable( $id ) . '</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );
      } else {
         $test = $rows[0];
     
         $this->printHtml( '<div class="aiTopNav aiFullWidth">' );
         $this->_displayFolderBreadCrumb( $test->test_folder_id );
         $this->printHtml( '</div>' );
      
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<form enctype="multipart/form-data" method="POST" action="' . $this->_baseurl . '/test/edit/">' );
         $this->printHtml( '<input type="hidden" value="save" name="action">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' ); 
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="2">Edit Test</th>' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' );

         if ( isset( $this->_error_message ) ) {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="2"><center><font color="#ff0000">' . $this->_error_message . '</font></td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td><input type="text" name="name" size="30" value="' . $this->escapeVariable( $test->name ) . '"></td>' );
         $this->printHtml( '</tr>' );

         $baseurl_obj = $test->getBaseUrl();

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Base URL:</td>' );
         if ( isset( $baseurl_obj ) ) {
            $this->printHtml( '<td><input type="text" name="baseurl" size="30" value="' . $this->escapeVariable( $baseurl_obj->baseurl ) . '"></td>' );
         } else {
            $this->printHtml( '<td><input type="text" name="baseurl" size="30" value=""></td>' );
         }
         $this->printHtml( '</tr>' );

         // lookup the description block
         $description_obj = $test->getDescription();

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="2">Description:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr class="odd">' );
         if ( isset( $description_obj ) ) {
            $this->printHtml( '<td colspan="2"><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $description_obj->description ) . '</textarea></td>' );
         } else {
            $this->printHtml( '<td colspan="2"><textarea name="description" rows="25" cols="60">Failed to find associated description.</textarea></td>' );
         }
         $this->printHtml( '</tr>' );

         $html_source_obj = $test->getHtmlSource();

         if ( $this->isFileUploadAvailable() ) {

            $this->printHtml( '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileUploadSize() . '">' ); 
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td>File:</td>' );
            $this->printHtml( '<td><input type="file" name="html_source_file"></td>' );
            $this->printHtml( '</tr>' ); 
         
         }

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="2"><center><input type="submit" value="Save"></center></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '</table>' );

         $this->printHtml( '</form>' );
        
         $this->printHtml( '</div>' );

         $commands = array();

         try {
            $sel = new CTM_Test_Command_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $id ) );
            $or_params = array();
            $order = array( 'id' );
            $commands = $sel->find( $and_params, $or_params, $order );
         } catch ( Exception $e ) {
         }

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Current Test</th>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td>Step</td>' );
         $this->printHtml( '<td>Command</td>' );
         $this->printHtml( '<td>Target</td>' );
         $this->printHtml( '<td>Value</td>' );
         $this->printHtml( '</tr>' );
         if ( count( $commands ) > 0 ) {
            $n = 0;
            $sel_comm_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Selenium_Command_Cache' );
            foreach ( $commands as $command ) {

               $n++;
               $class = $this->oddEvenClass();

               $sel_obj = $sel_comm_cache->getById( $command->test_selenium_command_id );
               $value_obj = $command->getValue();
               $target_obj = $command->getTarget();

               $this->printHtml( '<tr class="' . $class . '">' );
               $this->printHtml( '<td>' . $n . '</td>' );
               $this->printHtml( '<td>' . $sel_obj->name . '</td>' );
               $this->printHTml( '<td>' . $this->escapeVariable( $target_obj->target ) . '</td>' );
               $this->printHTml( '<td>' . $this->escapeVariable( $value_obj->value ) . '</td>' );
               $this->printHtml( '</tr>' );

            }
         } else {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="4"><center>- There are no test commands currently -</center></td>' );
            $this->printHtml( '</tr>' );
         }
         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );

      }

      $this->printHtml( '</center>' );

      return true;
   }

}

$test_edit_obj = new CTM_Site_Test_Edit();
$test_edit_obj->displayPage();
