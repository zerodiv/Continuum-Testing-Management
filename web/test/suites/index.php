<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );

class CTM_Site_Test_Suites extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Suites';
      return true;
   }

   public function displayBody() {
      $rows = array();
      try {
         $sel = new CTM_Test_Suite_Selector();
         $rows = $sel->find();

         print_r( $rows );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<center>' );
      $this->printHtml( '<br/><br/>' );
      $this->printHtml( '<table class="ctmTable">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="4">' . $this->_sitetitle . ': ' . $this->_pagetitle . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<td colspan="4" class="odd">' );
      $this->printHtml( '<center><a href="/test/suite/add/">Add Test Suite</a></center>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>Id</th>' );
      $this->printHtml( '<th>Last editor</th>' );
      $this->printHtml( '<th>Name</th>' );
      $this->printHtml( '<th>Description</th>' );
      $this->printHtml( '</tr>' );

      if ( count( $rows ) == 0 ) {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="even" colspan="5"><center><b>- There are no test suites are defiend -</b></td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );

      return true;
   }

}

$test_suites_obj = new CTM_Site_Test_Suites();
$test_suites_obj->displayPage();
