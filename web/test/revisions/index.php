<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Selector.php' );
require_once( 'CTM/Test/Command/Selector.php' );

class CTM_Site_Test_Edit extends CTM_Site { 
   private $_error_message;

   public function setupPage() {
      $this->_pagetitle = 'View Test Revisions';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );

      $test_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Cache' );
      $test = $test_cache->getById( $id );
      
      if ( ! isset( $test ) ) {
         $this->printHtml( '<div class="aiTableContainer">' );
         $this->printHtml( '<table class="ctmTable">' );
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Unable to find test: ' . $this->escapeVariable( $id ) . '</td>' );
         $this->printHtml( '</tr>' );
         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );
      } else {
     
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<form enctype="multipart/form-data" method="POST" action="' . $this->_baseurl . '/test/edit/">' );
         $this->printHtml( '<input type="hidden" value="save" name="action">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' ); 
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Test Revisions for: ' . $this->escapeVariable( $test->name ) . '</th>' );
         $this->printHtml( '</tr>' );

         if ( isset( $this->_error_message ) ) {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="2"><center><font color="#ff0000">' . $this->_error_message . '</font></td>' );
            $this->printHtml( '</tr>' );
         }

         $revisions = array();
         try {
            $sel = new CTM_Test_Revision_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'testId', '=', $id ) );
            $or_params = array();
            $order = array( 'id' );
            $revisions = $sel->find( $and_params, $or_params, $order );
         } catch ( Exception $e ) {
         }

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td>#</td>' );
         $this->printHtml( '<td>At:</td>' );
         $this->printHtml( '<td>By:</td>' );
         $this->printHtml( '<td>Action:</td>' );
         $this->printHtml( '</tr>' );

         if ( count( $revisions ) > 0 ) {
            $n = count( $revisions );
            foreach ( $revisions as $revision ) {

               $n--;

               $modifiedBy = $revision->getModifiedBy();

               $class = $this->oddEvenClass();

               $this->printHtml( '<tr class="' . $class . '">' );
               $this->printHtml( '<td>' . $n . '</td>' );
               $this->printHtml( '<td>' . $this->formatDate( $revision->modifiedAt ) . '</td>' );
               $this->printHtml( '<td>' . $this->escapeVariable( $modifiedBy->username ) . '</td>' );
               $this->printHtml( '<td><center>' );
               if ( $n == 0 ) {
               } else {
                  $this->printHtml( '<a href="' . $this->_baseurl . '/test/revision/diff/?id=' . $id . 
                        '&cur=' . $revision->id .
                        '&prev=' . $revisions[($n-1)]->id . '" class="ctmButton">Diff</a>' );
               }

               $this->printHtml( '</center></td>' );
               $this->printHtml( '</tr>' );

            }
         } else {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="4"><center>- There are no test revisions currently -</center></td>' );
            $this->printHtml( '</tr>' );
         }
         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );

      }

      $this->printHtml( '</center>' );

      return true;
   }

}

$test_edit_obj = new CTM_Site_Test_Edit();
$test_edit_obj->displayPage();
