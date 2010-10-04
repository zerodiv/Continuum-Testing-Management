<?php

class Light_MVC_Url_Checksum
{
   private $_magicKey;

   function __construct()
   {
      $this->_magicKey = 'MuFFinSauce';
   }

   private function _createChecksum( $params )
   {
      ksort($params);
      $joinedData = '';
      foreach ( $params as $k => $v ) {
         if ( $joinedData != '' ) {
            $joinedData .= '&';
         }
         $joinedData .= $k . '=' . $v;
      }
      return md5($joinedData . $this->_magicKey);
   }

   public function create( $params, $ttl = 60, $uriOutput = true )
   {

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
      $checksum = $this->_createChecksum($params);

      $params['checksum'] = $checksum;

      if ( $uriOutput == true ) {

         $uriString = '?';

         foreach ( $params as $k => $v ) {
            if ( $uriString != '?' ) {
               $uriString .= '&';
            }
            $uriString .= urlencode($k) . '=' . urlencode($v);
         }

         return $uriString;

      }

      return $params;

   }

   public function verify( Light_MVC $mvc, $fields )
   {
      
      $urlChecksum = $mvc->getOrPost('checksum', null);

      $tParams = array();
      $tParams[ 'ts' ]       = $mvc->getOrPost('ts', null);
      $tParams[ 'ttl' ]      = $mvc->getOrPost('ttl', null);

      foreach ( $fields as $field ) {
         $tParams[ $field ] = $mvc->getOrPost($field, null);
      }

      $expectedChecksum = $this->_createChecksum($tParams);

      if ( $urlChecksum == $expectedChecksum ) {
         // okay now verify the time stamp is within the value set
         $maxTime = $tParams['ts'] + $tParams['ttl'];

         if ( time() < $maxTime ) {
            return true;
         }

         // failed time check
         return false;

      }

      // failed checksum
      return false;

   }

}
