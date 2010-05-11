<?php

class CTM_Files
{
    protected $baseDir;
    protected $suiteFile;
    protected $suiteDir;
    protected $logFile;

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

    public function getSuite()
    {
        $suites =  glob($this->suiteDir . '/*/index.html');
        
        if (!empty($suites)) {
            return $suites[0];
        }

        throw new Exception("Could not find suite exception");
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
                    $this->remove($path . '/' . $item);
                }
            }
            $d->close();
            rmdir($path);
        }
    }

    public function  __destruct()
    {
        $this->remove($this->baseDir);
    }

    
}