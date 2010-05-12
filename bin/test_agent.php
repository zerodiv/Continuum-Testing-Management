#!/usr/bin/php
<?php

require_once dirname( __FILE__ ) . '/../bootstrap.php';
require_once 'Light/CommandLine/Script.php';
require_once 'CTM/Site/Config.php';
require_once 'CTM/Machine/Factory.php';
require_once 'CTM/Files.php';
require_once 'CTM/Test/Run.php';
require_once 'CTM/Test/Run/Selector.php';
require_once 'CTM/Test/Browser.php';
require_once 'CTM/Test/Browser/Selector.php';

class CTM_Test_Agent extends Light_CommandLine_Script
{
    protected $machine;
    protected $files;
    protected $testRunBrowserId;
    protected $downloadUrl;
    protected $testBrowser;
    protected $testStatus; // 1 success, 0 failure
    protected $testDuration;

    public function init()
    {
        $this->machine = CTM_Machine_Factory::factory();
        $this->files = new CTM_Files();
        $this->testStatus = 0;
        $this->testDuration = 0;
    }

    /**
     * @todo selenium-server.jar path should be inside the config?
     * @todo Is this going to execute on Windows or Mac?
     * 
     */
    public function run()
    {
        $this->message("Running CTM_Test_Agent.");
        $this->message("Requesting work.");
        $this->getTestData();

        if (!empty($this->downloadUrl)) {
            
            $this->message("Downloading test data from {$this->downloadUrl}.");
            $this->downloadTest();

            if ($this->testBrowser) {

                $this->message("Running suite.");

                // see -browserSideLog and -log for debugging information at
                // http://seleniumhq.org/docs/05_selenium_rc.html#selenium-server-logging
                $commandString = "java -jar selenium-server.jar -multiwindow -htmlSuite '*" . $this->testBrowser . "' 'http://www.adicio.com/' '" .  $this->files->getSuite() . "' '" . $this->files->getLogFile() . "'";

                $this->message("Running $commandString");

                $testStart = microtime(true);
                system($commandString, $returnValue);
                $testEnd = microtime(true);

                $this->testDuration = $testEnd - $testStart;

                if ($returnValue == 0) {
                    $this->message("############# Succeeded! #############");
                    $this->testStatus = 1;
                } else {
                    $this->message("############# Failed! #############");
                    $this->testStatus = 0;
                }

                $this->sendLog();
                
            } else {
                $this->message("No test browser defined");
            }
            
        } else {
            $this->message("Could not get the download URL.");
        }

    }

    /**
     * Polls the CTM server for work and extracts the test download url
     *
     */
    protected function getTestData()
    {
        $post_values = array();
        $post_values['guid'] = $this->machine->getGuid();

        $this->message("Post Values:\n" . print_r($post_values, true));

        $ch = curl_init(CTM_Site_Config::BASE_URL() . '/et/poll/1.0/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_values);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $return_xml = curl_exec($ch);
        $return_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $this->message("Return Status for getDownloadLink(): " . $return_status);
        $this->message("Return XML for getDownloadLink():\n" . $return_xml);

        try {
            
            // if we have a valid download url
            $xml = simplexml_load_string($return_xml);
            
            $this->testRunBrowserId = (string) @$xml->testRunBrowserId;
            $this->downloadUrl = (string) @$xml->downloadUrl;
            $this->testBrowser = (string) @$xml->testBrowser;

        } catch (Exception $e) {
            $this->message("Could not parse XML: {$e->getMessage()}");
            $e = null;
        }
    }

    /**
     * Downloads the test data to local disk for a given url
     *
     */
    protected function downloadTest()
    {
        $ch = curl_init($this->downloadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $return_data = curl_exec($ch);
        $return_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $this->message("Return Status for downloadTest(): " . $return_status);

        // write zip to disk
        $handle = fopen($this->files->getSuiteFile(), 'w');
        fwrite($handle, $return_data);
        fclose($handle);

        // extract suite zip
        system("unzip {$this->files->getSuiteFile()} -d {$this->files->getSuiteDir()}");
    }

    /**
     * Sends back output colleced during the agent's run back to the CTM server
     *
     */
    protected function sendLog()
    {
        // send the output back to CTM
        $ch = curl_init(CTM_Site_Config::BASE_URL() . '/et/log/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'testRunBrowserId' => $this->testRunBrowserId,
            'testDuration' => $this->testDuration,
            'testStatus' => $this->testStatus,
            'logData' => file_exists($this->files->getLogFile()) ? file_get_contents($this->files->getLogFile()) : null,
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $return_xml = curl_exec($ch);
        $return_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $this->message("Return Status for sendLog(): " . $return_status);
        $this->message("Return XML for sendLog():\n" . $return_xml);
    }

}


$ctm_test_agent = new CTM_Test_Agent();

