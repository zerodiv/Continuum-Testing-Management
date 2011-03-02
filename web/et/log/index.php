<?php

// bootstrap the include path
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'CTM/Site.php';
require_once 'CTM/Test/Run/Browser.php';
require_once 'CTM/Test/Run/Browser/Selector.php';
require_once 'CTM/Test/Run.php';
require_once 'CTM/Test/Run/Selector.php';
require_once 'CTM/Test/Run/Log.php';
require_once 'CTM/Test/Run/State.php';


class CTM_ET_Log extends CTM_Site
{

    public function setupPage()
    {
        return true;
    }

    private function _serviceOutput($status, $message)
    {
        echo "<?xml version=\"1.0\"?>\n";
        echo "<etResponse>\n";
        echo "   <version>1.0</version>\n";
        echo "   <status>$status</status>\n";
        echo "   <message>$message</message>\n";
        echo "</etResponse>\n";
    }

    /**
     *
     * @todo Lock tables!!!!
     * @return boolean
     */
    public function handleRequest()
    {
        $testRunBrowserId = $this->getOrPost('testRunBrowserId', '');
        $testStatus = $this->getOrPost('testStatus', '');
        $testDuration = $this->getOrPost('testDuration', '');
        $runLog = $this->getOrPost('runLog', '', false);
        $seleniumLog = $this->getOrPost('seleniumLog', '', false);

        // let's get the first test available browser run for this machine
        $test_run_browser_sel = new CTM_Test_Run_Browser_Selector();

        $and_params = array(
            new Light_Database_Selector_Criteria('id', '=', $testRunBrowserId),
        );

        $test_run_browsers = $test_run_browser_sel->find($and_params);

        if (!empty($test_run_browsers[0])) {

            // update test_run_browser state
            $test_run_browser = $test_run_browsers[0];
            $test_run_browser->testRunStateId = !empty($testStatus) ? CTM_Test_Run_State::STATE_COMPLETED : CTM_Test_Run_State::STATE_FAILED;
            $test_run_browser->save();

            // update test_run state

            $test_run_sel = new CTM_Test_Run_Selector();
            $and_params = array(
                new Light_Database_Selector_Criteria('id', '=', $test_run_browser->testRunId),
            );

            $test_runs = $test_run_sel->find($and_params);

            // valid test run?
            if (!empty($test_runs[0])) {

                $test_run = $test_runs[0];

                // only continue with test run state update if the test run is being executed
                // this is to prevent multiple test browser runs updating test run status when
                // a previous browser test fails
                if ($test_run->testRunStateId == CTM_Test_Run_State::STATE_EXECUTING) {

                    // if the test run browser failed, fail the test run
                    if ($test_run_browser->testRunStateId == CTM_Test_Run_State::STATE_FAILED) {
                        $test_run->testRunStateId = CTM_Test_Run_State::STATE_FAILED;
                        $test_run->save();
                    }

                    // if the test run browser completed, check to see if there are any other
                    // running tests... Do not update test run if we have other tests running.
                    if ($test_run_browser->testRunStateId == CTM_Test_Run_State::STATE_COMPLETED) {

                        // see if we have any other queued or running tests

                        $test_run_browser_other_sel = new CTM_Test_Run_Browser_Selector();

                        $and_params = array(
                            new Light_Database_Selector_Criteria('testRunId', '=', $test_run_browser->testRunId),
                            new Light_Database_Selector_Criteria('id', '!=', $test_run_browser->id),
                        );

                        $or_params = array(
                            new Light_Database_Selector_Criteria('testRunStateId', '=', CTM_Test_Run_State::STATE_QUEUED),
                            new Light_Database_Selector_Criteria('testRunStateId', '=', CTM_Test_Run_State::STATE_EXECUTING),
                        );

                        $test_run_browser_others = $test_run_browser_other_sel->find($and_params, $or_params);

                        // if not, update test_run state
                        if (count($test_run_browser_others) == 0) {
                            $test_run->testRunStateId = CTM_Test_Run_State::STATE_COMPLETED;
                            $test_run->save();
                        }
                    }
                }
            }

            // update the existance of a log for this run.
            $test_run_browser->hasLog = true;
            $test_run_browser->save();

            // Take the file and push it to our log location.
            $logPath = $test_run_browser->getLogPath();

            if ( ! is_dir($logPath) ) {
              mkdir($logPath, 0755, true);
            }

            // Write all the contents to disk.
            file_put_contents( $test_run_browser->getAgentLogFile(), $seleniumLog);
            file_put_contents( $test_run_browser->getJavaLogFile(), $runLog);

            $this->_serviceOutput('OK', '');

        } else {
            $this->_serviceOutput('FAIL', "Failed to find test run for this machine");
        }

        return false;
    }
}

$mainPage = new CTM_ET_Log();
$mainPage->displayPage();
