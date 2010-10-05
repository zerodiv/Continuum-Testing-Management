<?php

class Light_Config_Object
{

   private $_config;

   function __construct( $configFile = null )
   {

      if ( $configFile == null ) {
         $configFile = dirname(__FILE__) . '/../../../etc/config.ini';
      }

      $this->_config = null;
      $this->_config = parse_ini_file($configFile, true);

      if ( $this->_config == false ) {
         throw new Exception( 'Failed to parse config file: ' . $configFile );
      }


      // interpolate any variables.
      foreach ( $this->_config as $configNode => $configValues ) {
         foreach ( $configValues as $k => $v ) {
            if ( is_string($v) ) {
               // only parse string values.
               while ( preg_match('/\{(.*?)\:\:(.*?)\}/', $v, $regs) ) {
                  $tNamespace = $regs[1];
                  $tVariable = $regs[2];
                  $v = str_replace(
                      '{' . $tNamespace . '::' . $tVariable . '}',
                      $this->get($tNamespace, $tVariable),
                      $v 
                  );
               }
               $this->_config[ $configNode ][ $k ] = $v;
            }
         }
      }

   }

   public function get( $namespace, $variable )
   {
      $variable = strtolower($variable);
      if ( isset( $this->_config[ $namespace][ $variable ] ) ) {
         return $this->_config[$namespace][$variable];
      }
      return null;
   }

}
