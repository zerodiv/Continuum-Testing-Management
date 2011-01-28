<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite.php' );
require_once( 'CTM/Test/Suite/Description.php' );

class CTM_Site_Test_Suite_Add extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Folders');
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'user', 'qa', 'admin' ) );

      $testSuiteFolderId   = $this->getOrPost( 'testSuiteFolderId', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      if ( $name == '' ) {
         return true;
      }

      try {

         $user_obj = $this->getUser();

         // create the test suite and it's associated description.
         $new = new CTM_Test_Suite();
         $new->testSuiteFolderId = $testSuiteFolderId;
         $new->name = $name;
         $create_at = time(); // yes i know this is paranoia
         $new->createdAt = $create_at;
         $new->createdBy = $user_obj->id;
         $new->modifiedAt = $create_at;
         $new->modifiedBy = $user_obj->id;
         $new->testStatusId = 1; // all test_suites start life as pending
         $new->save();

         if ( $new->id > 0 ) {

            $new->setDescription( $description );

            $new->saveRevision();

            // added our child send us back to our parent
            header( 'Location: ' . $this->getBaseUrl() . '/test/suites/?parentId=' . $testSuiteFolderId );
            return false;
         }

      } catch ( Exception $e ) {
         // failed to insert.
         return true;
      }

      return true;

   }
                           

   public function displayBody() {
      $testSuiteFolderId   = $this->getOrPost( 'testSuiteFolderId', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/test/suite/add/">' );
      $this->printHtml( '<input type="hidden" value="' . $testSuiteFolderId . '" name="testSuiteFolderId">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="4">Add Test Suite</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Name:</td>' );
      $this->printHtml( '<td><input type="text" name="name" size="30" value="' . $this->escapeVariable( $name ) . '"></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Folder:</td>' );
      $this->printHtml( '<td>' . $this->_fetchFolderPath( $this->getBaseUrl() . '/tests/', $testSuiteFolderId, true ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2">Description:</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2"><center><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $description ) . '</textarea></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Add"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_suite_add_obj = new CTM_Site_Test_Suite_Add();
$test_suite_add_obj->displayPage();
