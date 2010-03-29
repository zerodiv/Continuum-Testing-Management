<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Selector.php' );
require_once( 'CTM/Test/Cache.php' );
require_once( 'CTM/Test/Folder/Cache.php' );
require_once( 'CTM/Test/Suite/Cache.php' );
require_once( 'CTM/Test/Suite/Plan.php' );
require_once( 'CTM/Test/Suite/Plan/Selector.php' );
require_once( 'CTM/Test/Suite/Plan/Type/Cache.php' );

class CTM_Site_Test_Suite_Plan extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Folders';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      $id                     = $this->getOrPost( 'id', '' );
      $action                 = $this->getOrPost( 'action', '' );
      $suite_id               = $this->getOrPost( 'suite_id', '' );
      $test_id                = $this->getOrPost( 'test_id', '' );
      $test_suite_id          = $this->getOrPost( 'test_suite_id', '' );
      $test_suite_plan_id     = $this->getOrPost( 'test_suite_plan_id', '' );

      if ( $action == 'remove_from_plan' ) {
         $target_test = null;
         try {

            // remove the target...
            $sel = new CTM_Test_Suite_Plan_Selector();

            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_suite_plan_id ) );
            $test_plans = $sel->find( $and_params );

            if ( count( $test_plans ) == 1 ) {
               $target_test = $test_plans[0];
               $target_test->remove();
            } else {
               return true;
            }

            // resequence the test_order
            $and_params = array( new Light_Database_Selector_Criteria( 'test_suite_id', '=', $test_suite_id ) );
            $or_params = array();
            $field_order = array( 'test_order' );

            $test_plans = $sel->find( $and_params, $or_params, $field_order );

            $test_order_id = 0;
            foreach ( $test_plans as $test_plan ) {
               $test_order_id++;
               $test_plan->test_order = $test_order_id;
               $test_plan->save();
            }

         } catch ( Exception $e ) {
         }
         return true;
      }

      // echo "id: $id action: $action suite_id: $suite_id\n";
      try {
         $sel = new CTM_Test_Suite_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );
         $test_suites = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $test_suite = null;
      if ( count( $test_suites ) == 1 ) {
         $test_suite = $test_suites[0];
      } else {
         // did not find the test suite in question.
         return true;
      }

      // Do updates if needed.
      if ( $action == 'move_item_down' || $action == 'move_item_up' ) {
         // lookup the test plan id in question.
         $test_plans = null;
         try {
            $sel = new CTM_Test_Suite_Plan_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_suite_id', '=', $test_suite_id ) );
            $or_params = array();
            $field_order = array( 'test_order' );
            $test_plans = $sel->find( $and_params, $or_params, $field_order );
         } catch ( Exception $e ) {
         }

         if ( count( $test_plans ) > 0 ) {

            // find the target item first.
            $target_item = null;
            $high_id = 0;

            foreach ( $test_plans as $test_plan ) {
               if ( $test_plan->id == $test_suite_plan_id ) {
                  $target_item = $test_plan;
               }
               if ( $high_id < $test_plan->test_order ) {
                  $high_id = $test_plan->test_order;
               }
            } 
            
            if ( ! isset( $target_item ) ) {
               return true;
            }

            $current_order_id = $target_item->test_order;

            if ( $action == 'move_item_down' ) {
               
               // this item is already at the max
               if ( $target_item->test_order == $high_id ) {
                  return true;
               }

               foreach ( $test_plans as $test_plan ) {
                  // find the next item and drop it by one
                  if ( $test_plan->test_order == ( $current_order_id + 1) ) {
                     $test_plan->test_order = $current_order_id;
                     $test_plan->save();
                  }
               }

               $target_item->test_order = $target_item->test_order + 1;
               $target_item->save();

            } // move_item_down

            if ( $action == 'move_item_up' ) {

               if ( $target_item->test_order == 1 ) {
                  return true;
               }

               foreach ( $test_plans as $test_plan ) {
                  // find the previous item and bump it up by one
                  if ( $test_plan->test_order == ( $current_order_id - 1 ) ) {
                     $test_plan->test_order = $current_order_id;
                     $test_plan->save();
                  }
               }

               $target_item->test_order = $target_item->test_order - 1;
               $target_item->save();

            } // move_item_up

         }

         return true;
      }

      // Determine the high id 
      $high_id = 0;

      $test_plans = null;
      try {
         $sel = new CTM_Test_Suite_Plan_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_suite_id', '=', $id ) );
         $test_plans = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      if ( count( $test_plans ) > 0 ) {
         foreach ( $test_plans as $test_plan ) {
            if ( $high_id < $test_plan->test_order ) {
               $high_id = $test_plan->test_order;
            }
         }
      }

      if ( $action == 'add_suite_to_plan' && isset( $suite_id ) && $suite_id > 0 ) {
         $test_plan = new CTM_Test_Suite_Plan();
         $test_plan->test_suite_id = $id;
         $test_plan->linked_id = $suite_id;
         $test_plan->test_order = ( $high_id + 1 );
         $test_plan->test_suite_plan_type_id = 1; // this is a suite
         $test_plan->save();
         return true;
      }

      if ( $action == 'add_test_to_plan' && isset( $test_id ) && $test_id > 0 ) {
         $test_plan = new CTM_Test_Suite_Plan();
         $test_plan->test_suite_id = $id;
         $test_plan->linked_id = $test_id;
         $test_plan->test_order = ( $high_id + 1 );
         $test_plan->test_suite_plan_type_id = 2; // this is a test
         $test_plan->save();
         return true;
      }

      return true;

      $rows = null;
      try {
         $sel = new CTM_Test_Suite_Selector();
         
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );

         $rows = $sel->find( $and_params );

      } catch ( Exception $e ) {
      }

      if ( isset( $rows[0] ) ) {
         $suite = $rows[0];
         $suite->name = $name;
         $suite->description = $description;
         $suite->modified_at = time();
         $suite->modified_by = $_SESSION['user']->id;
         $suite->save();
      }

      return true;

   }
                           

   public function displayBody() {
      $id               = $this->getOrPost( 'id', '' );
      $test_folder_id   = $this->getOrPost( 'test_folder_id', '' );

      $rows = null;
      $test_suite = null;
      $test_suite_plans = null;
      $test_suite_plan_type_cache = null;
      try {
         $sel = new CTM_Test_Suite_Selector();
         
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );

         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $test_suite = $rows[0];
         
            // pull up the plan 
            $test_suite_plan_type_cache = new CTM_Test_Suite_Plan_Type_Cache();
            
            $sel = new CTM_Test_Suite_Plan_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_suite_id', '=', $id ) );
            $or_params = array();
            $field_order = array( 'test_order' );
            $test_suite_plans = $sel->find( $and_params, $or_params, $field_order );

         }
      } catch ( Exception $e ) {
      }

      if ( isset( $test_suite ) ) {
         $this->printHtml( '<div class="aiTopNav">' );
         $this->_displayFolderBreadCrumb( $rows[0]->test_folder_id );
         $this->printHtml( '</div>' );
      
         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Plan Test Suite</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td colspan="4">Name: ' . $this->escapeVariable( $test_suite->name ) . '</td>' );
         $this->printHtml( '</tr>' );

         $this->oddEvenReset();

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Current Test Plan</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td class="aiColumnOne">Order</td>' );
         $this->printHtml( '<td>Type</td>' );
         $this->printHtml( '<td>Name</td>' );
         $this->printHtml( '<td>Action</td>' );
         $this->printHtml( '</tr>' );

         if ( count( $test_suite_plans ) > 0 ) {

            $plan_type_cache = new CTM_Test_Suite_Plan_Type_Cache();
            $test_suite_cache = new CTM_Test_Suite_Cache();
            $test_cache = new CTM_Test_Cache();

            $high_id = 0;

            foreach ( $test_suite_plans as $test_suite_plan ) {
               if ( $high_id < $test_suite_plan->test_order ) {
                  $high_id = $test_suite_plan->test_order;
               }
            }

            foreach ( $test_suite_plans as $test_suite_plan ) {

               $class = $this->oddEvenClass();

               $plan_type = $plan_type_cache->getById( $test_suite_plan->test_suite_plan_type_id );

               $test_suite = null;
               if ( $plan_type->name == 'suite' ) {
                  $test_suite = $test_suite_cache->getById( $test_suite_plan->linked_id );
               }

               $test = null;
               if ( $plan_type->name == 'test' ) {
                  $test = $test_cache->getById( $test_suite_plan->linked_id );
               }

               $this->printHtml('<tr class="' . $class . '">');
               $this->printHtml('<td><center>' );
               if ( $test_suite_plan->test_order != 0 && $test_suite_plan->test_order != $high_id ) {
                  $this->printHtml( '<a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&action=move_item_down&test_suite_plan_id=' . $test_suite_plan->id . '&test_suite_id=' . $test_suite_plan->test_suite_id . '">&darr;</a>' );
               } else {
                  $this->printHtml( '&nbsp;' );
               }
               $this->printHtml( $test_suite_plan->test_order );
               if ( $test_suite_plan->test_order != 0 && $test_suite_plan->test_order > 1 ) {
                  $this->printHtml( '<a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&action=move_item_up&test_suite_plan_id=' . $test_suite_plan->id . '&test_suite_id=' . $test_suite_plan->test_suite_id . '">&uarr;</a>' );
               } else {
                  $this->printHtml( '&nbsp;' );
               }
               $this->printHtml( '</center></td>' );
               $this->printHtml('<td>' . $plan_type->name . '</td>' );
               if ( isset( $test_suite ) ) {
                  $this->printHtml('<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
               } else if ( isset( $test ) ) {
                  $this->printHtml('<td>' . $this->escapeVariable( $test->name ) . '</td>' );
               } else {
                  $this->printHtml('<td>Could not find suite or test associated.</td>' );
               }
               $this->printHtml( '<td><center><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&test_suite_plan_id=' . $test_suite_plan->id . '&test_suite_id=' . $test_suite_plan->test_suite_id . '&action=remove_from_plan" class="ctmButton">Remove from plan</a></center></td>' );
               $this->printHtml('</tr>');
            }
         } else {
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="odd" colspan="4"><center>- There is no test plan for this suite -</center></td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Add Items to Plan</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td colspan="3">Current Position in Test Folders' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );

         $this->printHtml( '<td colspan="3">' );
         // Folder browser.
         $folder_cache = new CTM_Test_Folder_Cache();
         
         if ( $test_folder_id > 0 ) {
            // Look up the chain as needed.
            $parents = array();
            $folder_cache->getFolderParents( $test_folder_id, $parents );
            
            $parents = array_reverse( $parents );
            $this->printHtml( '<ul class="basictab">' );
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '">Test Folders</a></li>' );
            foreach ( $parents as $parent ) {
               $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&test_folder_id=' . $parent->id . '">' . $this->escapeVariable( $parent->name ) . '</a></li>' );
            }
            $this->printHtml( '</ul>' );


         } else {

            $this->printHtml( '<ul class="basictab">' );
            $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '">Test Folders</a></li>' );
            $this->printHtml( '</ul>' );

         }

         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' );


         $this->oddEvenReset();

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
               $this->printHtml( '<td colspan="3"><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&test_folder_id=' . $test_folder_row->id . '">' . $this->escapeVariable( $test_folder_row->name ) . '</a></td>' );
               $this->printHtml( '</tr>' );
            }
         } else {
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="row"><center>- No sub folders-</center></td>' );
            $this->printHtml( '</tr>' );
         }

         /*
            $hiearchy = $folder_cache->getHierarchy();
            $this->oddEvenReset();

            $this->printHtml( '<table class="ctmTable">' );

            if ( count( $hiearchy ) > 0 ) {
               $class = $this->oddEvenClass();
               $this->printHtml( '<tr>' );
               $this->printHtml( '<td class="' . $class . '"><ul>' );
               foreach ( $hiearchy as $hi_key => $hi ) {
                  $hi_key = str_replace( '[0]', ' - ', $hi_key );
                  $this->printHtml( '<li><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&test_folder_id=' . $hi->id . '">' . $this->escapeVariable( $hi_key ) . '</a></li>' );
               }
               $this->printHtml( '</ul></td>' );
               $this->printHtml( '</tr>' );
            } 
            */

         $suite_rows = null;
         try {
            $sel = new CTM_Test_Suite_Selector();
            $and_params = array( 
                  new Light_Database_Selector_Criteria( 'test_folder_id', '=', $test_folder_id ),
                  // we exclude ourselves from suites.
                  new Light_Database_Selector_Criteria( 'id', '!=', $id ) 
            ); 
            $suite_rows = $sel->find( $and_params );
         } catch ( Exception $e ) {
         } 
         
         $this->oddEvenReset(); 

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Test Suites</th>' );
         $this->printHtml( '</tr>' );
         
         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td class="aiColumnOne">ID</td>' );
         $this->printHtml( '<td>Name</td>' );
         $this->printHtml( '<td>Action</td>' );
         $this->printHtml( '</tr>' );
         
         if ( count( $suite_rows ) > 0 ) {
            foreach ( $suite_rows as $suite_row ) {
               $class = $this->oddEvenClass();

                  $this->printHtml( '<tr class="' . $class . '">' );
                  $this->printHtml( '<td>' . $suite_row->id . '</td>' );
                  $this->printHtml( '<td>' . $this->escapeVariable( $suite_row->name ) . '</td>' );
                  $this->printHtml( '<td><center><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&test_folder_id=' . $test_folder_id . '&action=add_suite_to_plan&suite_id=' . $suite_row->id . '" class="ctmButton">Add to plan</a></center></td>' );
                  $this->printHtml( '</tr>' );

               }
            } else {
               $this->printHtml( '<tr>' );
               $this->printHtml( '<td class="odd" colspan="3"><center>- No suites defined -</center></td>' );
               $this->printHtml( '</tr>' );
            }

         // pull all the tests in for this folder.
         $test_rows = null;
         try {
            $sel = new CTM_Test_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_folder_id', '=', $test_folder_id ) );
            $test_rows = $sel->find( $and_params );
         } catch ( Exception $e ) {
         }

         $this->oddEvenReset();

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Tests</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td class="aiColumnOne">ID</td>' );
         $this->printHtml( '<td>Name</td>' );
         $this->printHtml( '<td>Action</td>' );
         $this->printHtml( '</tr>' );

         if ( count( $test_rows ) > 0 ) {
            foreach ( $test_rows as $test_row ) {
               $class = $this->oddEvenClass();
               $this->printHtml( '<tr class="' . $class . '">' );
               $this->printHtml( '<td class="aiColumnOne">' . $test_row->id . '</td>' );
               $this->printHtml( '<td>' . $this->escapeVariable( $test_row->name ) . '</td>' );
               $this->printHtml( '<td><center><a href="' . $this->_baseurl . '/test/suite/plan/?id=' . $id . '&test_folder_id=' . $test_folder_id . '&action=add_test_to_plan&test_id=' . $test_row->id . '" class="ctmButton">Add to plan</a></center></td>' );
               $this->printHtml( '</tr>' );
            }
         } else {
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="odd" colspan="3"><center>- No tests defined -</center></td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '</table>' );
         $this->printHtml( '</div>' );

      }


      return true;
   }

}

$test_suite_plan_obj = new CTM_Site_Test_Suite_Plan();
$test_suite_plan_obj->displayPage();
