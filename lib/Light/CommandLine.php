<?php

require_once( 'Light/CommandLine/Config.php' );
require_once( 'Light/CommandLine/Option/Container.php' );

class Light_CommandLine {
   public $options;

   function __construct() {

      // setup the default timezone.
      date_default_timezone_set( Light_CommandLine_Config::DEFAULT_TIMEZONE() );


      $this->options = new Light_CommandLine_Option_Container();

      $this->main();

   }

   public function main() {
      $this->init();
      $this->run();
      $this->done();
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
