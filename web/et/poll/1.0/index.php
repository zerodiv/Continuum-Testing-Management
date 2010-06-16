<?php

// bootstrap the include path
require_once dirname(__FILE__) . '/../../../../bootstrap.php';
require_once('Light/Config.php');
require_once 'CTM/Site.php';
require_once 'CTM/Test/Run/Browser.php';
require_once 'CTM/Test/Run/Browser/Selector.php';
require_once 'CTM/Test/Machine.php';
require_once 'CTM/Test/Machine/Selector.php';
require_once 'CTM/Test/Run.php';
require_once 'CTM/Test/Run/Selector.php';
require_once 'CTM/Test/Browser.php';
require_once 'CTM/Test/Browser/Selector.php';
require_once 'CTM/Test/Run/BaseUrl/Selector.php';


class CTM_ET_Poll extends CTM_Site {

    public function setupPage() {
        $this->_pagetitle = 'Main';
        return true;
    }

    private function _findMachineByGuid( $guid ) {
        try {
            $sel = new CTM_Test_Machine_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'guid', '=', $guid ) );
            $rows = $sel->find( $and_params );
            if ( isset( $rows[0] ) ) {
                return $rows[0];
            }
            return null;
        } catch ( Exception $e ) {
        }
        return null;
    }

    private function findBrowserById($id) {
        $sel = new CTM_Test_Browser_Selector();
        $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );
        $rows = $sel->find( $and_params );
        if ( isset( $rows[0] ) ) {
            return $rows[0];
        }
        return null;
    }

    private function _serviceOutput($status, $message, $testRunId = null, $testRunBrowserId = null, $downloadUrl = null, $testBrowser = null, $testBaseurl = null ) {
        echo "<?xml version=\"1.0\"?>\n";
        echo "<etResponse>\n";
        echo "   <version>1.0</version>\n";
        echo "   <status>$status</status>\n";
        echo "   <message>$message</message>\n";
        if (!empty($testRunId)) {
            echo "   <testRunId>$testRunId</testRunId>\n";
        }
        if (!empty($testRunBrowserId)) {
            echo "   <testRunBrowserId>$testRunBrowserId</testRunBrowserId>\n";
        }
        if (!empty($downloadUrl)) {
            echo "   <downloadUrl>$downloadUrl</downloadUrl>\n";
        }
        if (!empty($testBrowser)) {
            echo "   <testBrowser>$testBrowser</testBrowser>\n";
        }
        if (!empty($testBaseurl)) {
            echo "   <testBaseurl>$testBaseurl</testBaseurl>\n";
        }
        echo "</etResponse>\n";
    }

    /**
     *
     * @todo Lock tables!!!!
     * @return boolean
     */
    public function handleRequest() {
        
        $guid = $this->getOrPost('guid', '' );

        if (empty($guid)) {
            $this->_serviceOutput( 'FAIL', "guid is required!" );
            return false;
        }

        // see if there is a test machine available for this hostname.
        $test_machine = $this->_findMachineByGuid($guid);

        if (!empty($test_machine)) {

            try {

                // let's get the first test available browser run for this machine
                $sel = new CTM_Test_Run_Browser_Selector();

                $and_params = array(
                        new Light_Database_Selector_Criteria('test_machine_id', '=', $test_machine->id),
                        new Light_Database_Selector_Criteria('test_run_state_id', '=', 1), // queued
                );

                $queued_rows = $sel->find($and_params, array(), array('id'), 1);

                // pick up any work that might of failed in progress
                $and_params = array(
                        new Light_Database_Selector_Criteria('test_machine_id', '=', $test_machine->id),
                        new Light_Database_Selector_Criteria('test_run_state_id', '=', 2), // executing
                );
                
                $executing_rows = $sel->find($and_params, array(), array('id'), 1);

                $rows = array();
                $rows = array_merge( $queued_rows, $executing_rows );

                if (!empty($rows[0])) {

                    // update test_run_browser state
                    $test_run_browser = $rows[0];

                    $downloadUrl = Light_Config::get( 'CTM_Site_Config', 'BASE_URL' ) . '/test/run/download/?id=' . $test_run_browser->test_run_id;

                    $test_run_browser->test_run_state_id = 2; // set state to "executing"
                    $test_run_browser->save();

                    $test_browser = $this->findBrowserById($test_run_browser->test_browser_id);
                    $testBrowser = $test_browser->name;

                    // update test_run state
                    $sel = new CTM_Test_Run_Selector();
                    $and_params = array(
                            new Light_Database_Selector_Criteria('id', '=', $test_run_browser->test_run_id),
                    );

                    $rows = $sel->find($and_params);

                    if (!empty($rows[0])) {
                        $test_run = $rows[0];
                        $test_run->test_run_state_id = 2; // set state to "executing"
                        $test_run->save();
                    }

                    // baseUrl from the first test.
                    $sel = new CTM_Test_Run_BaseUrl_Selector();
                    $and_params = array(
                          new Light_Database_Selector_Criteria('test_run_id', '=', $test_run_browser->test_run_id)
                    );
                    $test_run_baseurls = $sel->find( $and_params );

                    $test_run_baseurl = 'http://www.google.com';
                    if ( isset( $test_run_baseurls[0] ) ) {
                       $test_run_baseurl = $test_run_baseurls[0]->baseurl;
                    }

                    $this->_serviceOutput('OK', '', $test_run_browser->test_run_id, $test_run_browser->id, $downloadUrl, $testBrowser, $test_run_baseurl );

                } else {
                    $this->_serviceOutput('FAIL', "Failed to find test run for this machine");
                }

            } catch ( Exception $e ) {
                $this->_serviceOutput( 'FAIL', "failed to find test_machine" );
            }

        } else {
            $this->_serviceOutput( 'FAIL', "failed to find test_machine" );
        }

        return false;

    }

}

$mainPage = new CTM_ET_Poll();
$mainPage->displayPage();
