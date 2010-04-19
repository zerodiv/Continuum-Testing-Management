<?php

require_once( 'Light/CommandLine.php' );

class CTM_Test_Agent extends Light_CommandLine {
   public function init() {
      $this->options->add( new Light_CommandLine_Option( 'test_machine_id', '', true ) );
   }
   public function run() {
      $this->message( 'we should be doing something' );
      sleep( 240 );
   }
}
