<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_BaseUrl extends Light_Database_Object {
   public $id;
   public $test_id;
   public $baseurl;

   public function init() {
      $this->setSqlTable( 'test_baseurl' );
      $this->setDbName( 'test' );
   }

   public function cleanBaseUrl() {
      $parsed_url = parse_url( $this->baseurl );
      $baseurl = $parsed_url[ 'scheme' ] . ':' . $parsed_url['host'];
      if ( isset( $parsed_url['port'] ) && $parsed_url['port'] > 0 ) {
         $baseurl .= ':' . $parsed_url['port'];
      }
      $baseurl .= '/';
      return $baseurl;
   }

}
