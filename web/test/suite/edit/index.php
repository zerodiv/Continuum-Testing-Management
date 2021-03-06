<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite.php' );
require_once( 'CTM/Test/Suite/Selector.php' );

class CTM_Site_Test_Suite_Edit extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Folders');
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'user', 'qa', 'admin' ) );

      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      if ( $name == '' ) {
         return true;
      }

      $rows = null;
      try {
         $sel = new CTM_Test_Suite_Selector();
         
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );

         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $user_obj = $this->getUser();

            $suite = $rows[0];
            $suite->name = $name;
            $suite->modifiedAt = time();
            $suite->modifiedBy = $user_obj->id;
            $suite->save();

            $suite->setDescription( $description );

            $suite->saveRevision();

            header( 'Location: ' . $this->getBaseUrl() . '/test/suites/?parentId=' . $suite->testSuiteFolderId );

         }

      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      $test_suite = null;
      $description_obj = null;

      try {
         $sel = new CTM_Test_Suite_Selector();
         
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );

         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) { 
            $test_suite = $rows[0];
            $description_obj = $test_suite->getDescription();
         }


      } catch ( Exception $e ) {
      }

      if ( $test_suite ) {
         
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' ); 
         
         $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/test/suite/edit/">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' ); 
         
         $this->printHtml( '<table class="ctmTable aiFullWidth">' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Edit Test Suite</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td><input type="text" name="name" size="30" value="' . $this->escapeVariable( $test_suite->name ) . '"></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Folder:</td>' );
         $this->printHtml( '<td>' . $this->_fetchFolderPath( $this->getBaseUrl() . '/tests/', $test_suite->testSuiteFolderId, true ) . '</td>' );
         $this->printHtml( '</tr>' );
         
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="2">Description:</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="2"><center><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $description_obj->description ) . '</textarea></center></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Save"></center></td>' );
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
