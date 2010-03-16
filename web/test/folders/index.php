<?php

require_once( '../../../bootstrap.php' );
require_once( 'PFL/Site.php' );
require_once( 'PFL/Test/Folder/Selector.php' );

class PFL_Site_Test_Folders extends PFL_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function displayBody() {

      $rows = array();
      try {
         $sel = new PFL_Test_Folder_Selector();
         $rows = $sel->find();
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<center>' );
      $this->printHtml( '<br/><br/>' );
      $this->printHtml( '<table class="pflTable">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="4">Folders (<a href="/test/folder/add/">Add folder</a>)</th>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>Id</th>' );
      $this->printHtml( '<th>Name</th>' );
      $this->printHtml( '</tr>' );

      if ( count( $rows ) == 0 ) {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="even" colspan="5"><center><b>- There are no sub folders defined -</b></td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );

      return true;
   }

}

$test_folders_obj = new PFL_Site_Test_Folders();
$test_folders_obj->displayPage();
