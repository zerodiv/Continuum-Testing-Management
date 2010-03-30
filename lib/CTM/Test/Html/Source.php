<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/Command.php' );
require_once( 'CTM/Test/Command/Selector.php' );
require_once( 'CTM/Test/Selenium/Command.php' );
require_once( 'CTM/Test/Selenium/Command/Cache.php' );
require_once( 'CTM/Test/Selenium/Command/Selector.php' );

class CTM_Test_Html_Source extends Light_Database_Object {
   public $id;
   public $test_id;
   public $html_source;

   public function init() {
      $this->setSqlTable( 'test_html_source' );
      $this->setDbName( 'test' );
   }

   public function parseToTestCommands() {

      try {

         // if there are commands associated to this test purge them.
         $sel = new CTM_Test_Command_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'test_id', '=', $this->test_id ),
         );

         $command_rows = $sel->find( $and_params );

         if ( count( $command_rows ) ) {
            foreach ( $command_rows as $command_row ) {
               $command_row->remove();
            }
         }

         $test_command_cache = new CTM_Test_Selenium_Command_Cache();

         $html_source = stripslashes( $this->html_source ); 
         
         $xml = simplexml_load_string( $html_source );
        
         if ( isset( $xml->body->table->tbody->tr ) ) {
            foreach ( $xml->body->table->tbody->tr as $tr ) {
               list( $command, $target, $value ) = $tr->td;

               $command_obj = null;
               $command_obj = $test_command_cache->getByName( $command );

               if ( ! isset( $command_obj ) ) {
                  // add it to the database.
                  $command_obj = new CTM_Test_Selenium_Command();
                  $command_obj->name = $command;
                  $command_obj->save();
               }

               if ( isset( $command_obj ) && $command_obj->id > 0 ) {
                  $c = new CTM_Test_Command();
                  $c->test_id = $this->test_id;
                  $c->test_selenium_command_id = $command_obj->id;
                  $c->target = $target;
                  $c->value = $value;
                  $c->save();
               }

               /*
               echo "--------------------------------------------------------------------------------\n";
               echo "command    : $command\n";
               echo "target     : $target\n";
               echo "value      : $value\n";
               echo "--------------------------------------------------------------------------------\n";
               */

            }
         }

      } catch ( Exception $e ) {
      }

   }

}
