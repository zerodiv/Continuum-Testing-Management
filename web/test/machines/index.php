<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Machine/Selector.php' );

class CTM_Site_Test_Suites extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Machine');
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      $this->requiresRole( array( 'admin' ) );

      return true;

   }

   public function displayBody() {
      $rows = array();
      try {
         $sel = new CTM_Test_Machine_Selector();
         $rows = $sel->find();

      } catch ( Exception $e ) {
      }

      $this->printHtml( '<center>' );
      $this->printHtml( '<br/><br/>' );
      $this->printHtml( '<table class="ctmTable">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="7">' . $this->_sitetitle . ': ' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>Id</th>' );
      $this->printHtml( '<th>Machine Name</th>' );
      $this->printHtml( '<th>OS</th>' );
      $this->printHtml( '<th>GUID</th>' );
      $this->printHtml( '<th>Created At</th>' );
      $this->printHtml( '<th>Last Checkin</th>' );
      $this->printHtml( '<th>Action</th>' );
      $this->printHtml( '</tr>' );

      if ( count( $rows ) == 0 ) {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="even" colspan="7"><center><b>- There are no machines defiend -</b></td>' );
         $this->printHtml( '</tr>' );
      } else {
         foreach ( $rows as $row ) {
            $class = $this->oddEvenClass();
            $this->printHtml( '<tr class"' . $class . '">' );
            $this->printHtml( '<td>' . $row->id . '</td>' );
            $this->printHtml( '<td>' . $row->machine_name . '</td>' );
            $this->printHtml( '<td>' . $row->os . '</td>' );
            $this->printHtml( '<td>' . $row->guid . '</td>' );
            $this->printHtml( '<td>' . $this->formatDate( $row->createdAt ) . '</td>' );
            $this->printHtml( '<td>' . $this->formatDate( $row->last_modified ) . '</td>' );
            $this->printHtml( '<td>' );
            $this->printHtml( '<center><a href="' . $this->getBaseUrl() . '/test/machine/edit/?id=' . $row->id . '" class="ctmButton">Edit</a>' );
            $this->printHtml( '</td>' );
            $this->printHtml( '</tr>' );
         }
      }

      $this->printHtml( '</table>' );

      return true;
   }

}

$test_suites_obj = new CTM_Site_Test_Suites();
$test_suites_obj->displayPage();
