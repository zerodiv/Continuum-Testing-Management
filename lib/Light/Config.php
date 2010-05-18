<?php

// mini-factory esq interface to make a static callable interface for the config system.
class Light_Config {
   static private $_config_obj;

   public static function get( $namespace, $variable ) {
      if ( ! isset( Light_Config::$_config_obj ) ) {
         Light_Config::$_config_obj = new Light_Config_Impl();
      }
      return Light_Config::$_config_obj->get( $namespace, $variable );
   }
}

class Light_Config_Impl {

   private $_config;

   function __construct( $config_file = null ) {

      if ( $config_file == null ) {
         $config_file = dirname( __FILE__ ) . '/../../etc/config.ini';
      }

      $this->_config = null;
      $this->_config = parse_ini_file( $config_file, true );

      if ( $this->_config == false ) {
         throw new Exception( 'Failed to parse config file: ' . $config_file );
      }


      // interpolate any variables.
      foreach ( $this->_config as $config_node => $config_values ) {
         foreach ( $config_values as $k => $v ) {
            if ( is_string( $v ) ) {
               // only parse string values.
               while( preg_match( '/\{(.*?)\:\:(.*?)\}/', $v, $regs ) ) {
                  $t_namespace = $regs[1];
                  $t_variable = $regs[2];
                  $v = str_replace( 
                        '{' . $t_namespace . '::' . $t_variable . '}',
                        $this->get( $t_namespace, $t_variable ),
                        $v 
                  );
               }
               $this->_config[ $config_node ][ $k ] = $v;
            }
         }
      }

   }

   public function get( $namespace, $variable ) {
      $variable = strtolower( $variable );
      if ( isset( $this->_config[ $namespace][ $variable ] ) ) {
         return $this->_config[$namespace][$variable];
      }
      return null;
   }

}
