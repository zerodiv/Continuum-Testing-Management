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
       
       $writer = new XMLWriter();
       $writer->openMemory();
       $writer->setIndent(true);
       $writer->startDocument( '1.0', 'UTF-8' );

       $writer->startElement( 'etResponse' );

       $writer->writeElement( 'version', '1.0' );
       $writer->writeElement( 'status', $status );
       $writer->writeElement( 'message', $message );

       if (!empty($testRunId)) {
          $writer->writeElement( 'testRunId', $testRunId );
       }
       
       if (!empty($testRunBrowserId)) {
          $writer->writeElement( 'testRunBrowserId', $testRunBrowserId );
       }
       if (!empty($downloadUrl)) {
          $writer->writeElement( 'downloadUrl', $downloadUrl );
       }
       if (!empty($testBrowser)) {
          $writer->writeElement( 'testBrowser', $testBrowser );
       }
       if (!empty($testBaseurl)) {
          $writer->writeElement( 'testBaseurl', $testBaseurl );
       }
       $writer->endElement();
       $writer->endDocument();
       return $writer->outputMemory( true );
    }

    /**
     *
     * @todo Lock tables!!!!
     * @return boolean
     */
    public function handleRequest() {
        
        $guid = $this->getOrPost('guid', '' );

        header( 'Content-Type: text/xml' );

        if (empty($guid)) {
            echo $this->_serviceOutput( 'FAIL', "guid is required!" );
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
                       $first_url = $test_run_baseurls[0];
                       $test_run_baseurl = $first_url->cleanBaseUrl();
                    }

                    echo $this->_serviceOutput('OK', '', $test_run_browser->test_run_id, $test_run_browser->id, $downloadUrl, $testBrowser, $test_run_baseurl );
                    return false;

                } else {
                    echo $this->_serviceOutput('FAIL', "Failed to find test run for this machine");
                    return false;
                }

            } catch ( Exception $e ) {
                echo $this->_serviceOutput( 'FAIL', "Failed to find test_machine" );
                return false;
            }

        } else {
            echo $this->_serviceOutput( 'FAIL', "Failed to find test_machine" );
            return false;
        }

        return false;

    }

}

$mainPage = new CTM_ET_Poll();
$mainPage->displayPage();
