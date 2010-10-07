<?php

require_once( '../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Selector.php' );

class CTM_Site_Test_Revision_Diff extends CTM_Site { 
   private $_error_message;

   public function setupPage() {
      $this->setPageTitle('View Test Revisions');
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();
      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );
      $cur              = $this->getOrPost( 'cur', '' );
      $prev             = $this->getOrPost( 'prev', '' );

      $test_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Cache' );
      $test = $test_cache->getById( $id );
     
      $revision_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Revision_Cache' );
      $cur_obj = $revision_cache->getById( $cur );
      $prev_obj = $revision_cache->getById( $prev );

      if ( isset( $test ) && isset( $cur_obj ) && isset( $prev_obj ) ) {
         $rev_obj = new CTM_Revision_Framework( 'test' );
         list( $rv, $diff, $diff_err ) = $rev_obj->diffRevision( (int) $test->id, $cur_obj->revisionId, $prev_obj->revisionId );

         if ( $rv == true ) {
            $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
            
            $this->printHtml( '<table class="ctmTable aiFullWidth">' );
            
            $this->printHtml( '<tr>' );
            $this->printHtml( '<th colspan="2">View Revision:</th>' );
            $this->printHtml( '</td>' );
            $this->printHtml( '</tr>' );

            $lines = explode( "\n", $diff );

            $line_counter = 0;
            $in_file = false;
            foreach ( $lines as $line ) {
               if ( $in_file == true ) {
                  $line_counter++;
                  $this->printHtml( '<tr>' );
                  $this->printHtml( '<td>' . $line_counter . '</td>' );
                  $this->printHtml( '<td><pre>' . $this->escapeVariable( $line ) . '</pre></td>' );
                  $this->printHtml( '</tr>' );
               }
               if ( preg_match( '/@@.*@@/', $line ) ) {
                  $in_file = true;
               }
            }

            $this->printHtml( '</table>' );
            
            $this->printHtml( '</div>' );
         }

      }

      return true;
   }

}

$test_obj = new CTM_Site_Test_Revision_Diff();
$test_obj->displayPage();
