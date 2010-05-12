<?php

// bootstrap the include path
require_once dirname(__FILE__) . '/../../../bootstrap.php';
require_once 'CTM/Site.php';
require_once 'CTM/Site/Config.php';
require_once 'CTM/Test/Run/Browser.php';
require_once 'CTM/Test/Run/Browser/Selector.php';
require_once 'CTM/Test/Run.php';
require_once 'CTM/Test/Run/Selector.php';
require_once 'CTM/Test/Run/Log.php';


class CTM_ET_Log extends CTM_Site
{
    const TEST_RUN_STATUS_COMPLETED = 3;
    const TEST_RUN_STATUS_FAILED = 5;

    public function setupPage()
    {
        $this->_pagetitle = 'Main';
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
        $logData = $this->getOrPost('logData', '', false);

        if (!empty($testStatus)) {
            $testStatus = self::TEST_RUN_STATUS_COMPLETED;
        } else {
            $testStatus = self::TEST_RUN_STATUS_FAILED;
        }

        // let's get the first test available browser run for this machine
        $sel = new CTM_Test_Run_Browser_Selector();

        $and_params = array(
            new Light_Database_Selector_Criteria('id', '=', $testRunBrowserId),
        );

        $rows = $sel->find($and_params);

        if (!empty($rows[0])) {

            // update test_run_browser state
            $test_run_browser = $rows[0];
            $test_run_browser->test_run_state_id = $testStatus;
            $test_run_browser->save();

            // update test_run state

            // see if we have any other queued or running tests
            
            $sel = new CTM_Test_Run_Browser_Selector();
            
            $and_params = array(
                new Light_Database_Selector_Criteria('test_run_id', '=', $test_run_browser->test_run_id),
                new Light_Database_Selector_Criteria('id', '!=', $test_run_browser->id),
            );

            $or_params = array(
                new Light_Database_Selector_Criteria('test_run_state_id', '=', 1),
                new Light_Database_Selector_Criteria('test_run_state_id', '=', 2),
            );

            $rows = $sel->find($and_params, $or_params);

            // if not, update test_run state
            if (count($rows) == 0) {

                $sel = new CTM_Test_Run_Selector();
                $and_params = array(
                    new Light_Database_Selector_Criteria('id', '=', $test_run_browser->test_run_id),
                );

                $rows = $sel->find($and_params);

                if (!empty($rows[0])) {
                    $test_run = $rows[0];
                    $test_run->test_run_state_id = $testStatus;
                    $test_run->save();
                }
            }

            $test_run_log = new CTM_Test_Run_Log();
            $test_run_log->test_run_browser_id = $test_run_browser->id;
            $test_run_log->data = $logData;
            $test_run_log->duration = $testDuration;
            $test_run_log->created_at = time();
            $test_run_log->save();

            $this->_serviceOutput('OK', '');

        } else {
            $this->_serviceOutput('FAIL', "Failed to find test run for this machine");
        }

        return false;
    }
}

$mainPage = new CTM_ET_Log();
$mainPage->displayPage();
