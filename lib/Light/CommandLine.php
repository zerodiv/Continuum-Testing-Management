<?php

require_once( 'Light/CommandLine/Config.php' );

class Light_CommandLine {

   function __construct() {

      date_default_timezone_set( Light_CommandLine_Config::DEFAULT_TIMEZONE() );

      // $this->parseArguments();

      $this->init();

      $this->run();

      $this->done();

   }

   public function parseArguments() {
   }

   public function init() {
   }

   public function run() {
      $this->message( 'I should be overwritten!' );
      exit();
   }

   public function done( $rv = 0 ) {
      $this->message( 'Done!' );
      exit( $rv );
   }

   public function message( $message ) {
      echo date( 'r' ) . ' - ' . $message . "\n";
   }

}
