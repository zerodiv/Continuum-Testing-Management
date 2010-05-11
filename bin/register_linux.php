#!/usr/bin/php -q
<?php

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'Light/CommandLine/Script.php';
require_once 'CTM/Site/Config.php';
require_once 'CTM/Machine/Linux.php';

class CTM_Register_Linux extends Light_CommandLine_Script
{
    
   private $_hostname;

   public function init()
   {
      $this->_hostname = php_uname('n');
   }

   /**
    * @todo This script should be os independent. Create a factory method to handle
    * all operating systems.
    * 
    */
   public function run()
   {

      if ($this->_hostname == 'localhost') {
         $this->message('Hostname is set to localhost please provide a unique hostname.');
         $this->done(255);
      }

      if (!isset($this->_hostname)) {
         $this->message('Hostname was not detected please provide a unique hostname.');
         $this->done(255);
      }

      $this->message('Hostname as detected: ' . $this->_hostname);

      $machine = new CTM_Machine_Linux();

      $post_values = array();
      $post_values['guid'] = $machine->getGuid();
      $post_values['ip'] = $machine->getIp();
      $post_values['os'] = $machine->getOs();

      $browsers = $machine->getBrowsers();

      foreach ($browsers as $browser => $browser_version) {
          $post_values[$browser] = 'yes';
          $post_values[$browser . '_version'] = $browser_version;
      }

      $this->message("Post Values:\n" . print_r($post_values, true));

      $ch = curl_init(CTM_Site_Config::BASE_URL() . '/et/phone/home/1.0/');
      curl_setopt( $ch, CURLOPT_POST, true );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_values );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

      $return_xml = curl_exec($ch);
      $return_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      $this->message("Return Status: " . $return_status);
      $this->message("Return XML:\n" . $return_xml);

   }
}

$ctm_register_obj = new CTM_Register_Linux();
