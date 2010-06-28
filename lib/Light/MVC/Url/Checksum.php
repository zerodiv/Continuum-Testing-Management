<?php

class Light_MVC_Url_Checksum {
   private $_magicKey;

   function __construct() {
      $this->_magicKey = 'MuFFinSauce';
   }

   private function _createChecksum( $params ) {
      ksort( $params );
      $joined_data = '';
      foreach ( $params as $k => $v ) {
         if ( $joined_data != '' ) {
            $joined_data .= '&';
         }
         $joined_data .= $k . '=' . $v;
      }
      return md5( $joined_data . $this->_magicKey );
   }

   public function create( $params, $ttl = 60, $uri_output = true ) {

      if ( isset( $params['ts'] ) ) {
         throw new Exception( 'Failed to create checksum for uri, ts was provided as a param' );
      }

      if ( isset( $params['ttl'] ) ) {
         throw new Exception( 'Failed to create checksum for uri, ttl was provided as a param' );
      }

      if ( isset( $params['checksum'] ) ) {
         throw new Exception( 'Failed to create checksum for uri, checksum was provided as a param' );
      }

      $params['ts']  = time();
      $params['ttl'] = $ttl; 
    
      // calculate checksum
      $checksum = $this->_createChecksum( $params );

      $params['checksum'] = $checksum;

      if ( $uri_output == true ) {

         $uri_string = '?';

         foreach ( $params as $k => $v ) {
            if ( $uri_string != '?' ) {
               $uri_string .= '&';
            }
            $uri_string .= urlencode( $k ) . '=' . urlencode( $v );
         }

         return $uri_string;

      }

      return $params;

   }

   public function verify( Light_MVC $mvc, $fields ) {
      
      $url_checksum = $mvc->getOrPost( 'checksum', null );

      $t_params = array();
      $t_params[ 'ts' ]       = $mvc->getOrPost( 'ts', null );
      $t_params[ 'ttl' ]      = $mvc->getOrPost( 'ttl', null );

      foreach ( $fields as $field ) {
         $t_params[ $field ] = $mvc->getOrPost( $field, null );
      }

      $expected_checksum = $this->_createChecksum( $t_params );

      if ( $url_checksum == $expected_checksum ) {
         // okay now verify the time stamp is within the value set
         $max_time = $t_params['ts'] + $t_params['ttl'];

         if ( time() < $max_time ) {
            return true;
         }

         // failed time check
         return false;

      }

      // failed checksum
      return false;

   }

}
