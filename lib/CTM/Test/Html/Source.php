<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/User.php' );
require_once( 'CTM/Test/Command.php' );
require_once( 'CTM/Test/Command/Selector.php' );
require_once( 'CTM/Test/Selenium/Command.php' );
require_once( 'CTM/Test/Selenium/Command/Cache.php' );
require_once( 'CTM/Test/Selenium/Command/Selector.php' );
require_once( 'CTM/Test/Param/Library.php' );
require_once( 'CTM/Test/Param/Library/Cache.php' );

class CTM_Test_Html_Source extends Light_Database_Object {
   public $id;
   public $test_id;
   public $html_source;

   public function init() {
      $this->setSqlTable( 'test_html_source' );
      $this->setDbName( 'test' );
   }

   public function parseToTestCommands( CTM_User $user ) {

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
         $test_param_lib_cache = new CTM_Test_Param_Library_Cache();

         // pull out a store command so we have a id to work with.
         $store_command = $test_command_cache->getByName( 'store' );

         $html_source = stripslashes( $this->html_source ); 
         
         // TODO: Evailuate using something else to parse up the xml. This will have issues.
         $xml = simplexml_load_string( $html_source );
        
         if ( isset( $xml->body->table->tbody->tr ) ) {

            foreach ( $xml->body->table->tbody->tr as $tr ) {
               list( $command, $target, $value ) = $tr->td;

               // first lookup the slenium command object
               $command_obj = null;
               $command_obj = $test_command_cache->getByName( $command );

               if ( ! isset( $command_obj ) ) {
                  // add it to the database.
                  $command_obj = new CTM_Test_Selenium_Command();
                  $command_obj->name = $command;
                  $command_obj->save();
               }

               // if they have a command
               if ( isset( $command_obj ) && $command_obj->id > 0 ) {

                  // try to pull up the test lib object if available.
                  // we only care about input_variables at this time, output variables are moot
                  // see if there is a library item for this value.
                  $test_param_lib_obj = null;
                  if ( $command_obj->id == $store_command->id && preg_match( '/^ctm_input_(.*)/', (string) $value ) ) {
                     $test_param_lib_obj = $test_param_lib_cache->getByName( (string) $value );

                     if ( ! isset( $test_param_lib_obj ) ) {

                        $created_at = time();
                        $test_param_lib_obj = new CTM_Test_Param_Library();
                        $test_param_lib_obj->name = (string) $value;
                        $test_param_lib_obj->created_at = $created_at;
                        $test_param_lib_obj->created_by = $user->id;
                        $test_param_lib_obj->modified_at = $created_at;
                        $test_param_lib_obj->modified_by = $user->id;
                        $test_param_lib_obj->save();

                        if ( isset( $test_param_lib_obj->id ) ) {
                           $test_param_lib_obj->setDescription( '' );
                           $test_param_lib_obj->setDefault( $target );
                        }

                     }
                  }

                  // create the test command
                  $c = new CTM_Test_Command();
                  $c->test_id = $this->test_id;
                  $c->test_selenium_command_id = $command_obj->id;
                  $c->test_param_library_id = 0;

                  if ( isset( $test_param_lib_obj->id ) ) {
                     $c->test_param_library_id = $test_param_lib_obj->id;
                  }

                  $c->save();
                 
                  if ( isset( $c->id ) && $c->id > 0 ) {
                     $c->setTarget( $target );
                     $c->setValue( $value );
                  }

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
