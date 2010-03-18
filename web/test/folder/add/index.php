<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder.php' );

class CTM_Site_Test_Folder_Add extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {
      $parent_id = $this->getOrPost( 'parent_id', '' );
      $name = $this->getOrPost( 'name', '' );

      if ( $name == '' ) {
         return true;
      }

      try {
         $new_folder = new CTM_Test_Folder();
         $new_folder->parent_id = $parent_id;
         $new_folder->name = $name;
         $new_folder->save();
      } catch ( Exception $e ) {
         // failed to insert.
         return true;
      }

      // added our child send us back to our parent
      header( 'Location: ' . $this->_baseurl . '/test/folders/?parent_id=' . $parent_id );
      return false;

   }
                           

   public function displayBody() {
      $parent_id = $this->getOrPost( 'parent_id', '' );
      $name = $this->getOrPost( 'name', '' );

      $this->printHtml( '<center>' );

      $this->printHtml( '<table>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );
      $this->_displayFolderBreadCrumb( $parent_id );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );
      $this->printHtml( '<table class="ctmTable">' );
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/folder/add/">' );
      $this->printHtml( '<input type="hidden" value="' . $parent_id . '" name="parent_id">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">Add Folder</th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td class="odd">Name:</td>' );
      $this->printHtml( '<td class="odd"><input type="text" name="name" size="30" value="' . $name . '"></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Add"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</form>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );

      return true;
   }

}

$test_folder_add_obj = new CTM_Site_Test_Folder_Add();
$test_folder_add_obj->displayPage();
