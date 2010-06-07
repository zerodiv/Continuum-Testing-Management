<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Folder/Cache.php' );

class CTM_Site_Test_Run_Add extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Run - Add - Step 1 of 4';
      return true;
   }

   public function handleRequest() {
      $test_suite_id = $this->getOrPost( 'test_suite_id', '' );
      $iterations = $this->getOrPost( 'iterations', '' );

      $this->requiresAuth();

      try {

         if ( $test_suite_id > 0 ) {
            $user = $this->getUser();

            // create the provisional test.
            $test_run = new CTM_Test_Run();
            $test_run->test_suite_id = $test_suite_id;
            $test_run->test_run_state_id = 1;
            $test_run->iterations = 1; 
            $test_run->created_at = time();
            $test_run->created_by = $user->id;
            $test_run->save();

            $test_run->createTestRunCommands();

            if ( isset( $test_run->id ) ) {
               header( 'Location: ' . $this->_baseurl . '/test/run/add/step2/?id=' . $test_run->id );
            }

         }

      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $test_folder_id = $this->getOrPost( 'test_folder_id', 1 );
      $test_suite_id = $this->getOrPost( 'test_suite_id', '' );

      $test_folder_id += 0;
      $test_suite_id += 0;

      // folder_cacheing
      $folder_cache = new CTM_Test_Folder_Cache();

      // New style folder browser.
      $parents = array(); 
      $folder_cache->getFolderParents( $test_folder_id, $parents );
      $parents = array_reverse( $parents );
      $parents_cnt = count( $parents );

      $children = array();
      if ( $parents_cnt > 0 ) {
         $children = $folder_cache->getFolderChildren( $parents[ ($parents_cnt-1) ]->id );
      } 
      
      $folder_path = '';
      $current_parent = 0;
      foreach ( $parents as $parent ) {
         $current_parent++;
         $folder_path .= '/';
         $folder_path .= '<a href="' . $this->_baseurl . '/test/run/add/?test_folder_id=' . $parent->id . '">' . $parent->name . '</a>';
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">Add Test Run (Step 1 of 4)</th>' );
      $this->printHtml( '</tr>' );
      
      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td colspan="2">Pick a test suite from the test folders:</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Current folder path: ' . $folder_path . '</td>' );
      if ( count( $children ) > 0 ) {
         $this->printHtml( '<form action="' . $this->_baseurl . '/test/run/add/" method="POST">' );
         $this->printHtml( '<td>Sub Folders: <select name="test_folder_id">' );
         foreach ( $children as $child ) {
            $this->printHtml( '<option value="' . $child->id . '">' . $child->name . '</option>' );
         }
         $this->printHtml( '</select>' );
         $this->printHtml( '<input type="submit" value="Change Folder">' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</form>' );
      } else {
         $this->printHtml( '<td>No sub-folders available</td>' );
      }
      $this->printHtml( '</tr>' );

      $this->oddEvenReset();

      $test_suites = null;
      try {
         $sel = new CTM_Test_Suite_Selector();
         $and_params = array();
         $or_params = array( new Light_Database_Selector_Criteria( 'test_folder_id', '=', $test_folder_id ) );
         $order = array( 'name' );
         $test_suites = $sel->find( $and_params, $or_params, $order );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">Pick a test Suite: </th>' );
      $this->printHtml( '</tr>' );

      if ( count( $test_suites ) > 0 ) {
         foreach ( $test_suites as $test_suite ) {
            $class = $this->oddEvenClass();
            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td colspan="2"><a href="' . $this->_baseurl . '/test/run/add/?test_suite_id=' . $test_suite->id . '">' . $this->escapeVariable( $test_suite->name ) . '</a></td>' );
            $this->printHtml( '</tr>' );
         } 
      } else {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td colspan="2"><center>No test suites available for this folder.</center></td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_add_obj = new CTM_Site_Test_Run_Add();
$test_add_obj->displayPage();
