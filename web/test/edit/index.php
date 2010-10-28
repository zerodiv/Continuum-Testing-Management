<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Command/Selector.php' );

class CTM_Site_Test_Edit extends CTM_Site { 
   private $_error_message;

   public function setupPage() {
      $this->setPageTitle('Edit Test');
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'user', 'qa', 'admin' ) );

      $action           = $this->getOrPost( 'action', '' );
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $baseurl          = $this->getOrPost( 'baseurl', '' );
      $description      = $this->getOrPost( 'description', '' );

      if ( $name == '' ) {
         $this->_error_message = 'Test Name is a required field';
         return true;
      }

      if ( $baseurl == '' ) {
         $this->_error_message = 'Base URL is a required field';
         return true;
      }

      $user_obj = $this->getUser();
      $role_obj = $user_obj->getRole();

      $test_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Cache' );
      $test = $test_cache->getById( $id );

      if ( isset( $test ) && $role_obj->name == 'user' ) {
         $user_folder = $this->getUserFolder();
         if ( $test->testFolderId != $user_folder->id ) {
            header( 'Location: ' . $this->getBaseUrl() . '/user/permission/denied/' );
            return false;
         }
      }

      // -- save the cheerleader, save the world --
      if ( $action != 'save' ) {
         return true;
      }

      $had_file = false;

      $htmlSource = '';
      $htmlSourceFile = $_FILES['htmlSourceFile']['tmp_name'];

      if ( isset( $htmlSourceFile ) && filesize( $htmlSourceFile) > 0 ) {
         $htmlSource = file_get_contents( $htmlSourceFile );
         $had_file = true;
      }

      if ( isset( $test ) ) {

         try {

            $user_obj = $this->getUser();
            $test->name = $name;
            $test->modifiedAt = time();
            $test->modifiedBy = $user_obj->id;
            $test->revisionCount = $test->revisionCount + 1;
            // jeo: TODO: We will need to set this via a drop down eventually.
            // $test->testStatusId = 1; // all tests are created in a pending state.
            $test->save();

            $test->setDescription( $description );

            if ( $had_file == true ) {
               $test->setHtmlSource( $user_obj, $htmlSource );
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

            header( 'Location: ' . $this->getBaseUrl() . '/tests/?parentId=' . $test->testFolderId );
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
         $this->printHtml( '<form enctype="multipart/form-data" method="POST" action="' . $this->getBaseUrl() . '/test/edit/">' );
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

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Folder:</td>' );
         $this->printHtml( '<td>' . $this->_fetchFolderPath( $this->getBaseUrl() . '/tests/', $test->testFolderId ) . '</td>' );
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

         $htmlSourceObj = $test->getHtmlSource();

         if ( $this->isFileUploadAvailable() ) {

            $this->printHtml( '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileUploadSize() . '">' ); 
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td>File:</td>' );
            $this->printHtml( '<td><input type="file" name="htmlSourceFile"></td>' );
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
            $and_params = array( new Light_Database_Selector_Criteria( 'testId', '=', $id ) );
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

               $sel_obj = $sel_comm_cache->getById( $command->testSeleniumCommandId );
               $value_obj = $command->getValue();
               $target_obj = $command->getTarget();

               if ( $sel_obj->name == '#comment#' ) {
                  $this->printHtml( '<tr>' );
                  $this->printHtml( '<td class="comments" colspan="4"><center>' . $this->escapeVariable( $target_obj->target ) . '</center></td>' );
                  $this->printHtml( '</tr>' );
               } else {
                  $class = $this->oddEvenClass();

                  $this->printHtml( '<tr class="' . $class . '">' );
                  $this->printHtml( '<td>' . $n . '</td>' );
                  $this->printHtml( '<td>' . $sel_obj->name . '</td>' );
                  $this->printHTml( '<td>' . $this->escapeVariable( $target_obj->target ) . '</td>' );
                  $this->printHTml( '<td>' . $this->escapeVariable( $value_obj->value ) . '</td>' );
                  $this->printHtml( '</tr>' );
               }

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
