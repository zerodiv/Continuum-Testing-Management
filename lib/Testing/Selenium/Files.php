<?php

class Testing_Selenium_Files
{
    protected $baseDir;
    protected $suiteFile;
    protected $suiteDir;
    protected $logFile;
    protected $conversionDir;

    public function __construct()
    {
        $this->baseDir = sys_get_temp_dir() . '/' . uniqid('ctm_suite_');
        $this->suiteDir = $this->baseDir . '/suite';
        $this->conversionDir = $this->baseDir . "/conversion";
        
        $this->suiteFile = $this->baseDir . '/suite.zip';
        $this->logFile = $this->baseDir . '/log.txt';
        
        if (mkdir($this->baseDir) === false) {
            throw new Exception("Can not create base directory.");
        }

        if (mkdir($this->suiteDir) === false) {
            throw new Exception("Can not create suite directory.");
        }

        if (mkdir($this->conversionDir) === false) {
            throw new Exception("Can not create conversion directory.");
        }
    }

    public function getSuiteFile()
    {
        return $this->suiteFile;
    }

    public function getSuiteDir()
    {
        return $this->suiteDir;
    }

    public function getLogFile()
    {
        return $this->logFile;
    }
    
    public function getConversionDir()
    {
        return $this->conversionDir;
    }

    public function getSuites()
    {
        return glob($this->conversionDir . '/*/index.html');
    }

    /**
     * Generates individual test suites from the tests since we can not run them
     * 
     */
    public function convertTestsToSuites()
    {
        $testFiles = glob($this->suiteDir . "/*/*.html");

        foreach ($testFiles as $testFile) {

            $testName = basename($testFile, '.html');

            // we need to skip the original suite file
            if (strcasecmp($testName, 'index') == 0) {
                continue;
            }

            $suitePath = $this->conversionDir . "/" . $testName;

            // load the test xml
            try {
                $testXml = simplexml_load_file($testFile);
            } catch(Exception $e) {
                throw $e;
            }

            if (mkdir($suitePath, 0755, true) === true) {

                $testTitle = (string) $testXml->html->head->title;

                // generate suite file
                $suiteFile = "$suitePath/index.html";
                touch($suiteFile);
                file_put_contents($suiteFile, $this->getSuiteHtml($testTitle));
                // create a copy of test file
                symlink($testFile, "test.html");
                
            } else {
                throw new Exception('Failed to create new suite dir.');
            }
        }
    }

    /**
     * File or recursive directory removal
     *
     * @param string $path
     */
    public function remove($path)
    {
        if (is_file($path)) {
            unlink($path);
        }

        if (is_dir($path)) {
            $d = dir($path);
            while($item = $d->read()) {
                if ($item != "." && $item != "..") {
                    $this->remove($item);
                }
            }
            $d->close();
            rmdir($path);
        }
    }

    protected function getSuiteHtml($testTitle)
    {
        $html = '<html>
            <head>
            <title>Auto generated stuite for ' . $testTitle . '</title>
            </head>
            <body>
            <table>
            <tr><td><a href="./test">' . $testTitle . '</a></td></tr>
            </table>
            </body>
            </html>';

        return $html;
    }

    public function  __destruct()
    {
        $this->remove($this->suiteFile);
        $this->remove($this->suiteDir);
        $this->remove($this->logFile);
    }

    
}