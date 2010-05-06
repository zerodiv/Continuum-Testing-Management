#!/usr/bin/php -q
<?php

require_once dirname(__FILE__) . '/../bootstrap.php';
require_once 'Light/CommandLine.php';
require_once 'CTM/Config.php';

class CTM_Register_Linux extends Light_CommandLine
{
    
   private $_hostname;
   private $_ctm_host_id;

   public function init()
   {
      $this->_hostname        = php_uname('n');
      $this->_ctm_host_id     = null;
   }

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

      $post_values = array();
      $post_values['hostname'] = $this->_hostname;
      $post_values['os'] = PHP_OS;

      $browsers = array();
      $this->_findSafariBrowsers( $browsers );
      $this->_findChromeBrowsers( $browsers );
      $this->_findFirefoxBrowsers( $browsers );

      $this->message('browsers: ' . print_r($browsers, true));

      foreach ($browsers as $browser => $browser_version) {
         $post_values[ $browser ] = 'yes';
         $post_values[ $browser . '_version' ] = $browser_version;
      }

      // print_r( $post_values );

      // the request will return a xml for us to work with.
      $ch = curl_init('http://' . CTM_Site_Config::BASE_URL() . '/et/phone/home/1.0/');
      curl_setopt( $ch, CURLOPT_POST, true );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_values );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

      $return_xml = curl_exec($ch);
      $return_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      echo "return_status: $return_status\n";
      echo "return_xml: \n$return_xml\n";

   }
}

$ctm_register_obj = new CTM_Register_Linux();
