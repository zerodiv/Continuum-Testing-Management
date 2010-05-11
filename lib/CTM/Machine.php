<?php

abstract class CTM_Machine
{
    const MACHINE_BROWSER_FIREFOX = 'firefox';
    const MACHINE_BROWSER_EXPLORER = 'ie';
    const MACHINE_BROWSER_CHROME = 'chrome';
    const MACHINE_BROWSER_SAFARI = 'safari';

    protected $guid;
    protected $ip;
    protected $os;
    protected $browsers = array();


    public function getGuid()
    {
        return $this->guid;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getOs()
    {
        return $this->os;
    }

    public function getBrowsers()
    {
        return $this->browsers;
    }

    protected function init()
    {
        $this->findGuid();
        $this->findIp();
        $this->findOs();
        $this->findBrowsers();
    }

    abstract public function findGuid();
    abstract public function findIp();
    abstract public function findOs();
    abstract public function findBrowsers();

}

?>
