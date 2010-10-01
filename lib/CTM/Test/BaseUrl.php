<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_BaseUrl extends Light_Database_Object
{
   public $id;
   public $testId;
   public $baseurl;

   public function init()
   {
      $this->setSqlTable('ctm_test_baseurl');
      $this->setDbName('test');
   }

   public function cleanBaseUrl()
   {
      $parsedUrl = parse_url($this->baseurl);
      $baseurl = $parsedUrl[ 'scheme' ] . '://' . $parsedUrl['host'];
      if ( isset( $parsedUrl['port'] ) && $parsedUrl['port'] > 0 ) {
         $baseurl .= ':' . $parsedUrl['port'];
      }
      $baseurl .= '/';
      return $baseurl;
   }

}
