#!/usr/bin/php -q
<?php

require_once( dirname( __FILE__ ) . '/../bootstrap.php' );
require_once( 'CTM/Test/Selector.php' );

// pull data from the db.
try {
   $sel = new CTM_Test_Selector();
   $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', 1 ) );
   $rows = $sel->find( $and_params );

   if ( isset( $rows[0] ) ) {
      echo "found test\n";
      $test = $rows[0];

      $test->html_source = stripslashes( $test->html_source );

      echo "test:\n";
      echo $test->html_source . "\n";

      $xml = simplexml_load_string( $test->html_source );

      if ( isset( $xml->body->table->tbody->tr ) ) {
         foreach ( $xml->body->table->tbody->tr as $tr ) {

            list( $command, $target, $value ) = $tr->td;

            echo "command    : $command\n";
            echo "target     : $target\n";
            echo "value      : $value\n";

            // instanciate a target Selenium RC object, and run this item through.

         }
      }

      // send the test to the targeted box.
   }
} catch ( Exception $e ) {
}
