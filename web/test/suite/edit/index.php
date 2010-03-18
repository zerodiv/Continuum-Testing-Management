<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite.php' );
require_once( 'CTM/Test/Suite/Selector.php' );

class CTM_Site_Test_Suite_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {
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

      } catch ( Exception $e ) {
      }

      if ( isset( $rows[0] ) ) {
         $suite = $rows[0];
         $suite->name = $name;
         $suite->description = $description;
         $suite->modified_at = time();
         $suite->modified_by = $_SESSION['user']->id;
         $suite->save();
      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );
      $name             = $this->getOrPost( 'name', '' );
      $description      = $this->getOrPost( 'description', '' );

      $rows = null;
      try {
         $sel = new CTM_Test_Suite_Selector();
         
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );

         $rows = $sel->find( $and_params );

      } catch ( Exception $e ) {
      }

      $this->printHtml( '<center>' );

      $this->printHtml( '<table>' );

      if ( isset( $rows[0] ) ) {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td valign="top">' );
         $this->_displayFolderBreadCrumb( $rows[0]->test_folder_id );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' ); 
      }
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );
      $this->printHtml( '<table class="ctmTable">' );

      if ( isset( $rows[0] ) ) {
         $test_suite = $rows[0];
         $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/suite/edit/">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Edit Test Suite</th>' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Name:</td>' );
         $this->printHtml( '<td class="odd"><input type="text" name="name" size="30" value="' . $this->escapeVariable( $test_suite->name ) . '"></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2">Description:</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">' . $this->escapeVariable( $test_suite->description ) . '</textarea></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Save"></center></td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '</form>' );
      }

      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );

      return true;
   }

}

$test_suite_edit_obj = new CTM_Site_Test_Suite_Edit();
$test_suite_edit_obj->displayPage();
