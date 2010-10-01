<?php 
require_once( '../../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run/BaseUrl/Selector.php' );

class CTM_Site_Test_Run_Add_Step3 extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Run - Add - Step 3 of 4');
      return true;
   }

   public function handleRequest() {
      $id = $this->getOrPost( 'id', '' );
      $action = $this->getOrPost( 'action', '' );

      $this->requiresAuth();

      if ( $action == 'step3' ) {

         $testRunBaseurls = $this->getOrPost( 'testRunBaseurl', '' );

         if ( count( array_keys($testRunBaseurls) ) > 0 ) {
            try {
               $sel = new CTM_Test_Run_BaseUrl_Selector();

               foreach ( $testRunBaseurls as $b_id => $b_url ) {
                  $base_obj = null;
                  $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $b_id ) );
                  $base_objs = $sel->find( $and_params );
                  if ( isset( $base_objs[0] ) ) {
                     $base_obj = $base_objs[0];
                     $base_obj->baseurl = $b_url;
                     $base_obj->save();
                  }
               }

               header( 'Location: ' . $this->getBaseUrl() . '/test/run/add/step4/?id=' . $id);
               return false;

            } catch ( Exception $e ) {
            }
         }

      }
      return true;

   }
                           

   public function displayBody() {
      $test_run_id = $this->getOrPost( 'id', '' );
      $test_run = null;
      $test_suite = null;
      $base_urls = null;

      try {

         $sel = new CTM_Test_Run_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run_id ) );
         $test_runs = $sel->find( $and_params );
         
         if ( isset( $test_runs[0] ) ) {
            $test_run = $test_runs[0];
         }

         if ( isset( $test_run->id ) ) {
            $sel = new CTM_Test_Suite_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run->test_suite_id ) );
            $test_suites = $sel->find( $and_params );

            if ( isset( $test_suites[0] ) ) {
               $test_suite = $test_suites[0];
            }

            $sel = new CTM_Test_Run_BaseUrl_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'test_run_id', '=', $test_run->id ) );
            $base_urls = $sel->find( $and_params ); 
         }
      } catch ( Exception $e ) {
      }

      if ( isset( $test_run->id ) ) {

         
         $test_run_state_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Run_State_Cache' );
         $step3 = $test_run_state_cache->getByName('step3');

         $test_run->test_run_state_id = $step3->id;
         $test_run->save();

         $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );

         $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/test/run/add/step3/">' );
         $this->printHtml( '<input type="hidden" name="action" value="step3">' );
         $this->printHtml( '<input type="hidden" name="id" value="' . $test_run->id .'">' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Add Test Run (Step 3 of 4)</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="odd">' );
         $this->printHtml( '<td>Test Suite:</td>' );
         $this->printHtml( '<td>' . $this->escapeVariable( $test_suite->name ) . '</td>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '</table>' );

         $this->printHtml( '<table class="ctmTable aiFullWidth">' );

         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="3">Test Urls</th>' );
         $this->printHtml( '</tr>' );

         $this->printHtml( '<tr class="aiTableTitle">' );
         $this->printHtml( '<td>Component:</td>' );
         $this->printHtml( '<td>Name:</td>' );
         $this->printHtml( '<td>Url:</td>' );
         $this->printHtml( '</tr>' );

         $suite_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Suite_Cache' );
         $test_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Cache' );

         foreach ( $base_urls as $base_url ) {
            $class = $this->oddEvenClass();

            $component_type = '';
            $component_text = '';

            if ( $base_url->test_suite_id > 0 ) {
               $suite_obj = $suite_cache->getById( $base_url->test_suite_id );
               $component_type = 'suite';
               $component_text = $suite_obj->name;
            } else if ( $base_url->testId > 0 ) {
               $test_obj = $test_cache->getById( $base_url->testId );
               $component_type = 'test';
               $component_text = $test_obj->name;
            }

            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td>' . $component_type . '</td>' );
            $this->printHtml( '<td>' . $component_text . '</td>' );
            $this->printHtml( '<td><input type="text" size="80" value="' . $this->escapeVariable( $base_url->baseurl ) . '" name="testRunBaseurl[' . $base_url->id .']"></td>' );
            $this->printHtml( '</tr>' );
         }

         $this->printHtml( '<tr class="aiButtonRow">' );
         $this->printHtml( '<td colspan="3"><center><input type="submit" value="Next: Configure Browsers"></center></td>' );
         $this->printHtml( '</tr>' ); 

         $this->printHtml( '</table>' );

         $this->printHtml( '</form>' );

         $this->printHtml( '</div>' );

      } 

      return true;

   }

}

$test_add_obj = new CTM_Site_Test_Run_Add_Step3();
$test_add_obj->displayPage();
