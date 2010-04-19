<?php

require_once( 'Light/CommandLine/Config.php' );

class Light_CommandLine {

   function __construct() {

      // setup the default timezone.
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

   public function formatDate( $timestamp ) {
      return date( Light_CommandLine_Config::TIME_FORMAT(), $timestamp );
   }

   public function message( $message ) {
      echo $this->formatDate( time() ) . ' - ' . $message . "\n";
   }

}
