<?php

require_once( '../../../../bootstrap.php' );
require_once( 'PFL/Site.php' );
require_once( 'PFL/Test/Suite.php' );

class PFL_Site_Test_Suite_Add extends PFL_Site { 

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
         $new = new PFL_Test_Suite();
         $new->test_folder_id = $test_folder_id;
         $new->name = $name;
         $new->description = $description;
         $create_at = time(); // yes i know this is paranoia
         $new->created_at = $create_at;
         $new->modified_at = $create_at;
         $new->modified_by = $_SESSION['user']->id;
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

      $this->printHtml( '<table class="pflTable">' );
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

      return true;
   }

}

$test_suite_add_obj = new PFL_Site_Test_Suite_Add();
$test_suite_add_obj->displayPage();
