<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_BaseUrl extends Light_Database_Object
{
   public $id;
   public $testRunId;
   public $testSuiteId;
   public $testId;
   public $baseurl;

   public function init()
   {
      $this->setSqlTable('ctm_test_run_baseurl');
      $this->setDbName('test');
   }

   public function cleanBaseUrl()
   {
      $parsedUrl = parse_url($this->baseurl);
      if ( ! isset($parsedUrl['scheme']) ) {
         // unable to find the http or https
         return null;
      }
      if ( ! isset($parsedUrl['host']) ) {
         // unable to find the hostname
         return null;
      }
      $baseurl = $parsedUrl[ 'scheme' ] . '://' . $parsedUrl['host'];
      if ( isset( $parsedUrl['port'] ) && $parsedUrl['port'] > 0 ) {
         $baseurl .= ':' . $parsedUrl['port'];
      }
      $baseurl .= '/';
      return $baseurl;
   }


}
