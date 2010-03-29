<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Folder.php' );
require_once( 'CTM/Test/Folder/Selector.php' );

class CTM_Site_Test_Folder_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      $id = $this->getOrPost( 'id', '' );
      $name = $this->getOrPost( 'name', '' );

      if ( $name == '' ) {
         return true;
      }

      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );

         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $folder = $rows[0];
            $folder->name = $name;
            $folder->save();
         }

      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $id = $this->getOrPost( 'id', '' );

      $rows = null;
      $folder = null;

      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );

         $rows = $sel->find( $and_params );
      
         if ( count( $rows ) == 1 ) {
            $folder = $rows[0];
         }

      } catch ( Exception $e ) {
      }

      $name = $this->getOrPost( 'name', '' );

      if ( isset( $folder ) ) {
         $this->printHtml( '<div class="aiTopNav">' );
         $this->_displayFolderBreadCrumb( $rows[0]->parent_id );
         $this->printHtml( '</div>' );

         $this->printHtml( '<div class="aiTableContainer">' );
         $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/folder/edit/">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' );
         $this->printHtml( '<table class="ctmTable">' );
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Edit Folder</th>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td><input type="text" name="name" size="30" value="' . $folder->name . '"></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="2"><center><input type="submit" value="Save"></center></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '</table>' );

         $this->printHtml( '</form>' ); 
         $this->printHtml( '</div>' );

      }

      return true;
   }

}

$test_folder_edit_obj = new CTM_Site_Test_Folder_Edit();
$test_folder_edit_obj->displayPage();
