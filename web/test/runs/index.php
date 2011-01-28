<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run/Selector.php' );

class CTM_Site_Test_Runs extends CTM_Site { 

    public function setupPage() {
        $this->setPageTitle('Test Runs');
        return true;
    }

    public function handleRequest() {
        $this->requiresAuth();
        return true;
    }

    public function displayBody() {
        $run_state_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Run_State_Cache' );
        $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );
        $test_machine_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Machine_Cache' );
        $test_browser_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Browser_Cache' );

        $queued_state = $run_state_cache->getByName('queued');
        $executing_state = $run_state_cache->getByName('exeecuting');
        $completed_state = $run_state_cache->getByName('completed');
        $archived_state = $run_state_cache->getByName('archived');
        $failed_state = $run_state_cache->getByName('failed');
        $step1_state = $run_state_cache->getByName('step1');
        $step2_state = $run_state_cache->getByName('step2');
        $step3_state = $run_state_cache->getByName('step3');
        $step4_state = $run_state_cache->getByName('step4');

        $role_obj = $this->getUser()->getRole();

        if ( ! isset( $run_state->id ) ) {
            // load up the queued page if we cannot find the current run state.
            $run_state = $run_state_cache->getById( 1 );
        }

        $test_runs = null;
        try {
            $sel = new CTM_Test_Run_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'testRunStateId', '!=', $archived_state->id ) );
            $test_runs = $sel->find( $and_params );
        } catch ( Exception $e ) {
        }

        $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
        $this->printHtml( '<table class="ctmTable aiFullWidth">' );

        $this->printHtml( '<tr>' );
        $this->printHtml( '<th colspan="7">Test Runs</th>' );
        $this->printHtml( '</tr>' );

        $this->printHtml( '<tr class="aiButtonRow">' );
        $this->printHtml( '<td colspan="7"><center><a href="' . $this->getBaseUrl() . '/test/run/add" class="ctmButton">Add Test Run</a></center></td>' );
        $this->printHtml( '</tr>' );

        if ( count( $test_runs ) == 0 ) {
            $this->printHtml( '<tr class="odd">' );
            $this->printHtml( '<td colspan="7"><center> - There are no test runs in a non archived state.</center></td>' );
            $this->printHtml( '</tr>' );
        } else {
            $this->printHtml( '<tr class="aiTableTitle">' );
            $this->printHtml( '<td class="aiColumnOne">ID</td>' );
            $this->printHtml( '<td>Test Suite</td>' );
            $this->printHtml( '<td>Status</td>' );
            $this->printHtml( '<td>Created At</td>' );
            $this->printHtml( '<td>Created By</td>' );
            $this->printHtml( '</tr>' );
            foreach ( $test_runs as $test_run ) {


                $class = $this->oddEvenClass();
                $createdBy = $user_cache->getById( $test_run->createdBy );

                // lookup the test suite that is associated to this test run.
                $test_suite = null;
                try {
                    $sel = new CTM_Test_Suite_Selector();
                    $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $test_run->testSuiteId ) );
                    $test_suites = $sel->find( $and_params );
                    if ( isset( $test_suites[0] ) ) {
                        $test_suite = $test_suites[0];
                    }
                } catch ( Exception $e ) {
                }

                if ($test_run->testRunStateId == 5) {
                    $testRunColor = '#FF0000';
                } else {
                    $testRunColor = '#00FF00';
                }


                $run_state = $run_state_cache->getById($test_run->testRunStateId);

                $testRunLink = '<a href="' . $this->getBaseUrl() . '/test/run/?id=' . $test_run->id . '">';

                $this->printHtml( '<tr class="' . $class . '">' );
                $this->printHtml( '<td class="aiColumnOne">' . $testRunLink . $test_run->id . '</a></td>' );
                $this->printHtml( '<td>' . $testRunLink . $this->escapeVariable( $test_suite->name ) . '</a></td>' );
                $this->printHtml('<td style="background-color:' . $testRunColor . ';"><center>' . $testRunLink . $run_state->description . '</a></center></td>');
                $this->printHtml( '<td>' . $testRunLink . $this->formatDate( $test_run->createdAt ) . '</a></td>' );
                $this->printHtml( '<td>' . $testRunLink . $this->escapeVariable( $createdBy->username ) . '</a></td>' );
                $this->printHtml( '</tr>' );

            }
        }

        $this->printHtml( '<tr class="aiButtonRow">' );
        $this->printHtml( '<td colspan="7"><center><a href="' . $this->getBaseUrl() . '/test/run/add" class="ctmButton">Add Test Run</a></center></td>' );
        $this->printHtml( '</tr>' );

        $this->printHtml( '</table>' );
        $this->printHtml( '</div>' );

        return true;
    }

}

$test_obj = new CTM_Site_Test_Runs();
$test_obj->displayPage();
