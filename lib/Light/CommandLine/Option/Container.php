<?php

require_once( 'Light/CommandLine/Option.php' );

class Light_CommandLine_Option_Container {
   private $_args;

   function __construct() {
      $this->_args = array();
   }

   public function add( Light_CommandLine_Option $option ) {
      $this->_args[] = $option;
   }

   public function get( $name ) {
      foreach ( $this->_args as $arg ) {
         if ( $arg->getName() == $name ) {
            return $arg;
         }
      }
      return null;
   }

   // TODO: Handle boolean arguments.
   // TODO: Handle multiple copies / data for arguments.
   public function parse( Light_CommandLine $script ) {

      foreach ( $this->_args as &$arg ) {
         foreach ( $_SERVER['argv'] as $arg_value) {
            if ( preg_match( '/^--' . $arg->getName() . '\=(.*)$/', $arg_value, $arg_values ) ) {
               $arg->setValue( $arg_values[1] );
            }
         }
      }

   }


}
