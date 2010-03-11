<?php

class Light_CommandLine {

   function __construct() {

      date_default_timezone_set( Light_CommandLine_Config::DEFAULT_TIMEZONE() );

      // $this->parseArguments();

      $this->run();

   }

   public function parseArguments() {
   }

   public function run() {
      $this->message( 'I should be overwritten!' );
      exit();
   }

   private function message( $message ) {
      $debug_data = debug_backtrace();

      $stack_string = '';
      if ( isset( $debug_data[1]['class'] ) ) {
         $stack_string .= $debug_data[1]['class'] . '::';
      }
      $stack_string .= $debug_data[1]['function'];

      echo date( 'r' ) . ' - ' . $stack_string . ' - ' . $message . "\n";
   }

}
