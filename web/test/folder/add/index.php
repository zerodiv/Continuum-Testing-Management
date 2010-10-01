<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder.php' );

class CTM_Site_Test_Folder_Add extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Folders');
      return true;
   }

   public function handleRequest() {
      
      $this->requiresAuth();

      $parentId = $this->getOrPost( 'parentId', '' );
      $name = $this->getOrPost( 'name', '' );

      if ( $name == '' ) {
         return true;
      }

      try {
         $new_folder = new CTM_Test_Folder();
         $new_folder->parentId = $parentId;
         $new_folder->name = $name;
         $new_folder->save();
      } catch ( Exception $e ) {
         // failed to insert.
         return true;
      }

      // added our child send us back to our parent
      header( 'Location: ' . $this->getBaseUrl() . '/tests/?parentId=' . $parentId );
      return false;

   }
                           

   public function displayBody() {
      $parentId = $this->getOrPost( 'parentId', '' );
      $name = $this->getOrPost( 'name', '' );

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );

      $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/test/folder/add/">' );
      $this->printHtml( '<input type="hidden" value="' . $parentId . '" name="parentId">' );

      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">Add Folder</th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Folder:</td>' );
      $this->printHtml( '<td>' . $this->_fetchFolderPath( $this->getBaseUrl() . '/tests/', $parentId ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Name:</td>' );
      $this->printHtml( '<td><input type="text" name="name" size="30" value="' . $name . '"></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="2"><center><input type="submit" value="Add"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</form>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_folder_add_obj = new CTM_Site_Test_Folder_Add();
$test_folder_add_obj->displayPage();
