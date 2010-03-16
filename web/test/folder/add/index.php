<?php

require_once( '../../../../bootstrap.php' );
require_once( 'PFL/Site.php' );
require_once( 'PFL/Test/Folder.php' );

class PFL_Site_Test_Folder_Add extends PFL_Site { 

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
         $new_folder = new PFL_Test_Folder();
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

      $this->printHtml( '<table class="pflTable">' );
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/folder/add/">' );
      $this->printHtml( '<input type="hidden" value="' . $parent_id . '" name="parent_id">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="4">Add Folder</th>' );
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

      return true;
   }

}

$test_folder_add_obj = new PFL_Site_Test_Folder_Add();
$test_folder_add_obj->displayPage();
