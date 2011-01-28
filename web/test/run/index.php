<?php

require_once( '../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Suite/Selector.php' );
require_once( 'CTM/Test/Run/Selector.php' );

class CTM_Site_Test_Run extends CTM_Site { 
    private $_testRun;
    private $_suite;

    public function setupPage() {
        $id = $this->getOrPost('id', null);

        try {

            $test_run_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Run_Cache' );
            $this->_testRun = $test_run_cache->getById($id);

            if ( ! isset( $this->_testRun->id ) ) {
                echo 'Failed to find test run by id: ' . $id;
                return false;
            }

            $suite_cache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Suite_Cache');
            $this->_suite = $suite_cache->getById($this->_testRun->testSuiteId);

            if ( ! isset( $this->_suite->id ) ) {
                echo 'Failed to find test run by id: ' . $id . ' testSuiteId: ' . $this->_testRun->testSuiteId;
                return false;
            }

        } catch ( Exception $ex ) {
            echo 'Failed to find test run by id: ' . $id;
            print_r($ex);
            return false;
        }

        $this->setPageTitle('Test Run: ' . $test);
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

        $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
        $this->printHtml( '<table class="ctmTable aiFullWidth">' );

        $this->printHtml( '<tr>' );
        $this->printHtml( '<th colspan="7">Test Run: ' . $this->escapeVariable($this->_suite->name) . '</th>' );
        $this->printHtml( '</tr>' );

        $this->printHtml( '<tr class="aiTableTitle">' );
        $this->printHtml( '<td class="aiColumnOne">ID</td>' );
        $this->printHtml( '<td>Test Suite</td>' );
        $this->printHtml( '<td>Status</td>' );
        $this->printHtml( '<td>Created At</td>' );
        $this->printHtml( '<td>Created By</td>' );
        $this->printHtml( '<td>Actions</td>' );
        $this->printHtml( '</tr>' );

        $class = $this->oddEvenClass();
        $createdBy = $user_cache->getById( $this->_testRun->createdBy ); 

        // lookup the test suite that is associated to this test run.  
        if ($this->_testRun->testRunStateId == 5) {
            $testRunColor = '#FF0000';
        } else {
            $testRunColor = '#00FF00';
        } 

        $run_state = $run_state_cache->getById($this->_testRun->testRunStateId); 

        $this->printHtml( '<tr class="' . $class . '">' );
        $this->printHtml( '<td class="aiColumnOne">' . $testRun->id . '</td>' );
        $this->printHtml( '<td>' . $this->escapeVariable( $this->_suite->name ) . '</td>' );
        $this->printHtml('<td style="background-color:' . $testRunColor . ';"><center>' . $run_state->description . '</center></td>');
        $this->printHtml( '<td>' . $this->formatDate( $this->_testRun->createdAt ) . '</td>' );
        $this->printHtml( '<td>' . $this->escapeVariable( $createdBy->username ) . '</td>' );

        $this->printHtml( '<td><center>' );
        if ( $this->_testRun->testRunStateId != $step1_state->id &&
                $this->_testRun->testRunStateId != $step2_state->id &&
                $this->_testRun->testRunStateId != $step3_state->id &&
                $this->_testRun->testRunStateId != $step4_state->id 
           ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/download/?id=' . $this->_testRun->id . '" class="ctmButton">Download</a>' );
        }
        if ($this->_testRun->testRunStateId == $step1_state->id ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/add/?id=' . $this->_testRun->id . '" class="ctmButton">' . $this->escapeVariable( $step1_state->description ) . '</a>' );
        }
        if ($this->_testRun->testRunStateId == $step2_state->id ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/add/step2/?id=' . $this->_testRun->id . '" class="ctmButton">' . $this->escapeVariable( $step2_state->description ) . '</a>' );
        }
        if ($this->_testRun->testRunStateId == $step3_state->id ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/add/step3/?id=' . $this->_testRun->id . '" class="ctmButton">' . $this->escapeVariable( $step3_state->description ) . '</a>' );
        }
        if ($this->_testRun->testRunStateId == $step4_state->id ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/add/step4/?id=' . $this->_testRun->id . '" class="ctmButton">' . $this->escapeVariable( $step4_state->description ) . '</a>' );
        }

        // while a test is executing we cannot do any admin actions to it.
        $displayRemove = false;
        $displayRetest = true;
        if ( $this->_testRun->testRunStateId == $queued_state->id || 
                $this->_testRun->testRunStateId == $completed_state->id ||
                $this->_testRun->testRunStateId == $failed_state->id ||
                $this->_testRun->testRunStateId == $archived_state->id ) {
            $displayRemove = true;
        } 

        if ( $role_obj->name == 'admin' ) {
            $displayRemove = true;
        }

        if ( $displayRetest == true ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/retest/?testRunId=' . $this->_testRun->id . '" class="ctmButton">Re-Test</a>' );
        }

        if ( $displayRemove == true ) {
            $this->printHtml( '<a href="' . $this->getBaseUrl() . '/test/run/remove/?testRunId=' . $this->_testRun->id . '" class="ctmButton">Remove</a>' );
        }

        $this->printHtml( '</center></td>' );
        $this->printHtml( '</tr>' ); 

        $sel = new CTM_Test_Run_Browser_Selector();
        $and_params = array(new Light_Database_Selector_Criteria('testRunId', '=', $this->_testRun->id));
        $test_run_browsers = $sel->find($and_params); 

        if (count($test_run_browsers) > 0) {

            $this->printHtml('<tr><td valign="top" colspan="7">' );
            $this->printHtml( '<table class="ctmTable aiFullWidth">' );
            $this->printHtml('<tr class="aiTableTitle">');
            $this->printHtml('<td colspan="6">Test Browser Runs for: ' . $this->escapeVariable( $test_suite->name ) . '</td>');
            $this->printHtml('</tr>');

            $this->printHtml('<tr class="aiTableTitle">' );
            $this->printHtml('<td>ID</td>' );
            $this->printHtml('<td>OS</td>' );
            $this->printHtml('<td>Browser</td>' );
            $this->printHtml('<td>Status</td>' );
            $this->printHtml('<td>Actions</td>' );
            $this->printHtml('</tr>' );

            foreach ($test_run_browsers as $test_run_browser) {

                // failed?
                if ($test_run_browser->testRunStateId == 5) {
                    $testRunBrowserColor = '#FF0000';
                } else {
                    $testRunBrowserColor = '#00FF00';
                }

                $test_machine = $test_machine_cache->getById( $test_run_browser->testMachineId );
                $t_machine = $test_machine->ip;
                if ( $test_machine->machineName != '' ) {
                    $t_machine = $test_machine->machineName;
                }

                $test_browser = $test_browser_cache->getById( $test_run_browser->testBrowserId );

                $this->printHtml('<tr class="' . $this->oddEvenClass() . '">');
                $this->printHtml('<td>' . $test_run_browser->id . '</td>');
                $this->printHtml('<td>' . $test_machine->os . ' @ ' . $t_machine . '</td>');
                $this->printHtml('<td>' . $test_browser->getPrettyName() . ' - ' . $test_browser->getPrettyVersion() . '</td>');
                $this->printHtml('<td style="background-color:' . $testRunBrowserColor . ';"><center>' . $run_state_cache->getById($test_run_browser->testRunStateId)->name . '</center></td>');
                $this->printHtml('<td><center>');
                if ( $test_run_browser->hasLog == true ) {
                    $this->printHtml('<a href="' . $this->getBaseUrl() . '/test/run/browser/log/?testRunBrowserId=' . $test_run_browser->id . '&type=selenium" class="ctmButton" target="_blank">Selenium Log</a>');
                    $this->printHtml('<a href="' . $this->getBaseUrl() . '/test/run/java/log/?testRunBrowserId=' . $test_run_browser->id . '&type=selenium" class="ctmButton" target="_blank">Java Server Log</a>');
                }
                $this->printHtml('<a href="' . $this->getBaseUrl() . '/test/run/browser/remove/?testRunBrowserId=' . $test_run_browser->id . '" class="ctmButton">Remove Run</a>');
                $this->printHtml('</center></td>');
                $this->printHtml('</tr>');
            }

            $this->printHtml('</table>');
            $this->printHtml('</td>' );
            $this->printHtml('</tr>' );

        }

        $this->printHtml( '<tr class="aiButtonRow">' );
        $this->printHtml( '<td colspan="7"><center><a href="' . $this->getBaseUrl() . '/test/run/add" class="ctmButton">Add Test Run</a></center></td>' );
        $this->printHtml( '</tr>' );

        $this->printHtml( '</table>' );
        $this->printHtml( '</div>' );

        return true;
    }

}

$test_obj = new CTM_Site_Test_Run();
$test_obj->displayPage();
