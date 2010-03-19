<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite.php' );

class CTM_Site_Test_Suite_Add extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {
      $test_folder_id   = $this->getOrPost( 'test_folder_id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      if ( $name == '' ) {
         return true;
      }

      try {
         $new = new CTM_Test_Suite();
         $new->test_folder_id = $test_folder_id;
         $new->name = $name;
         $new->description = $description;
         $create_at = time(); // yes i know this is paranoia
         $new->created_at = $create_at;
         $new->created_by = $_SESSION['user']->id;
         $new->modified_at = $create_at;
         $new->modified_by = $_SESSION['user']->id;
         $new->test_status_id = 1; // all test_suites start life as pending
         $new->save();
      } catch ( Exception $e ) {
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
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/suite/add/">' );
      $this->printHtml( '<input type="hidden" value="' . $test_folder_id . '" name="test_folder_id">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="4">Add Test Suite</th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Name:</td>' );
      $this->printHtml( '<td class="odd"><input type="text" name="name" size="30" value="' . $name . '"></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2">Description:</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">' . $description . '</textarea></td>' );
      $this->printHtml( '</tr>' );

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

$test_suite_add_obj = new CTM_Site_Test_Suite_Add();
$test_suite_add_obj->displayPage();
