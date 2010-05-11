<?php

require_once 'CTM/Machine.php';

class CTM_Machine_Linux extends CTM_Machine
{
    public function __construct()
    {
        parent::init();
    }
    
    public function findGuid()
    {
        exec('hostid', $output, $return);

        if ((string) $return === '0') {

            if (!empty($output) && is_array($output)) {
                $this->guid = array_pop($output);
            }
            
        }
    }

    public function findIp()
    {
        preg_match_all('/inet addr:\s?([^\s]+)/', `ifconfig`, $ips);
        $this->ip =  $ips[1][0]; // this is unreliable
    }

    public function findOs()
    {
        exec('uname -sp', $output, $return);

        if ((string) $return === '0') {

            if (!empty($output) && is_array($output)) {
                $this->os = array_pop($output);
            }
            
        }
    }

    public function findBrowsers()
    {
        $this->findFirefox();
        $this->findChrome();
    }

    protected function findFirefox()
    {
        exec('which firefox', $output, $return);

        if ((string) $return === '0') {

            if (!empty($output) && is_array($output)) {
                
                $path = array_pop($output); // because of aliases

                exec("$path -v", $versionOutput);

                if (!empty($versionOutput) && is_array($versionOutput)) {

                    $versionString = array_pop($versionOutput);
                    
                    if (preg_match('#\d+\.\d+\.\d+#', $versionString, $versionMatches) > 0) {
                        $this->browsers[self::MACHINE_BROWSER_FIREFOX] = $versionMatches[0];
                    }
                }
            }
        }

    }

    /**
     * @todo Chrome implementation missing
     */
    protected function findChrome()
    {
        
    }

}

?>
