<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Folder/Cache.php' );

class CTM_Site_Test_Run_Add extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Run - Add';
      return true;
   }

   public function handleRequest() {
      $test_suite_id = $this->getOrPost( 'test_suite_id', '' );
      $test_run_id = $this->getOrPost( 'test_run_id', '' );
      $iterations = $this->getOrPost( 'iterations', '' );

      $this->requiresAuth();

      try {

         if ( $test_run_id > 0 ) {
         }

         if ( $test_suite_id > 0 ) {
            $user = $this->getUser();

            $test_run = new CTM_Test_Run();
            $test_run->test_suite_id = $test_suite_id;
            $test_run->test_run_state_id = 1;
            $test_run->iterations = 1; 
            $test_run->created_at = time();
            $test_run->created_by = $user->id;
            $test_run->save();

            $test_run->createTestRunCommands();

            if ( isset( $test_run->id ) ) {
               $_POST['test_run_id'] = $test_run->id;
            }

         }

      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $test_folder_id = $this->getOrPost( 'test_folder_id', '' );
      $test_run_id = $this->getOrPost( 'test_run_id', '' );
      $test_suite_id = $this->getOrPost( 'test_suite_id', '' );

      $test_folder_id += 0;
      $test_run_id += 0;
      $test_suite_id += 0;

      if ( $test_run_id > 0 ) {

         $test_run = null;
         $test_suite = null;
         try {

            $sel = new CTM_Test_Run_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run_id ) );
            $test_runs = $sel->find( $and_params );

            if ( isset( $test_runs[0] ) ) {
               $test_run = $test_runs[0];
            }

            if ( isset( $test_run->id ) ) {
               $sel = new CTM_Test_Suite_Selector();
               $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_suite_id ) );
               $test_suites = $sel->find( $and_params );

               if ( isset( $test_suites[0] ) ) {
                  $test_suite = $test_suites[0];
               }
            }
         } catch ( Exception $e ) {
         }

         if ( isset( $test_run->id ) ) {
            $this->printHtml( '<table class="ctmTable">' );

            $this->printHtml( '<tr>' );
            $this->printHtml( '<th colspan="2">Add Test Run</th>' );
            $this->printHtml( '</tr>' );

            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td>Test Suite:</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
            $this->printHtml( '</tr>' );

            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td>Iterations:</td>' );
            $this->printHtml( '<td><input type="text" name="iterations" size="3" value="1"></td>' );
            $this->printHtml( '</tr>' );

            $this->printHtml( '</table>' );
         }

         return true;
      }

      $this->printHtml( '<table class="ctmTable">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="3">Add Test Run</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<td colspan="3">' );
      
      // Folder browser.
      $folder_cache = new CTM_Test_Folder_Cache();
      
      if ( $test_folder_id > 0 ) {
         // Look up the chain as needed.
         $parents = array();
         $folder_cache->getFolderParents( $test_folder_id, $parents );
         $parents = array_reverse( $parents );
         $this->printHtml( '<ul class="basictab">' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/run/add/">Test Folder</a></li>' );
         foreach ( $parents as $parent ) {
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/run/add/?test_folder_id=' . $parent->id . '">' . $this->escapeVariable( $parent->name ) . '</a></li>' );
         }
         $this->printHtml( '</ul>' ); 
      } else { 
         $this->printHtml( '<ul class="basictab">' );
         $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/run/add/">Test Folders</a></li>' );
         $this->printHtml( '</ul>' ); 
      } 
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->oddEvenReset();

      $test_folder_rows = null;
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'parent_id', '=', $test_folder_id ) ); 
         $test_folder_rows = $sel->find( $and_params );
      } catch ( Exception $e ) {
      } 
      
      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td colspan="3">Sub Folders</td>' );
      $this->printHtml( '</tr>' );
      
      if ( count( $test_folder_rows ) > 0 ) {
         foreach ( $test_folder_rows as $test_folder_row ) {
            $class = $this->oddEvenClass();
            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td colspan="3"><a href="' . $this->_baseurl . '/test/run/add/?test_folder_id=' . $test_folder_row->id . '">' . $this->escapeVariable( $test_folder_row->name ) . '</a></td>' );
            $this->printHtml( '</tr>' );
         }
      } else {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="row"><center>- No sub folders-</center></td>' );
         $this->printHtml( '</tr>' );
      }
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

      foreach ( $test_suites as $test_suite ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr class="' . $class . '">' );
         $this->printHtml( '<td><a href="' . $this->_baseurl . '/test/run/add/?test_suite_id=' . $test_suite->id . '">' . $this->escapeVariable( $test_suite->name ) . '</a></td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );

      return true;
   }

}

$test_add_obj = new CTM_Site_Test_Run_Add();
$test_add_obj->displayPage();
