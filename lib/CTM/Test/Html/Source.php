<?php

require_once( 'Light/Database/Object.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );

require_once( 'CTM/Test/Html/Source/Parser.php' );

require_once( 'CTM/User.php' );
require_once( 'CTM/Test.php' );
require_once( 'CTM/Test/Selector.php' );
require_once( 'CTM/Test/Command.php' );
require_once( 'CTM/Test/Command/Selector.php' );
require_once( 'CTM/Test/Selenium/Command.php' );
require_once( 'CTM/Test/Selenium/Command/Selector.php' );
require_once( 'CTM/Test/Param.php' );
require_once( 'CTM/Test/Param/Library.php' );


class CTM_Test_Html_Source extends Light_Database_Object {
   public $id;
   public $test_id;
   public $html_source;

   public function init() {
      $this->setSqlTable( 'test_html_source' );
      $this->setDbName( 'test' );
   }

   public function save( CTM_User $user ) {

      parent::save();

      // pass the user down into the save.
      $this->parseToTestCommands( $user );

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

         $test_command_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Selenium_Command_Cache' );
         $test_param_lib_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Param_Library_Cache' );

         // pull out a store command so we have a id to work with.
         $store_command = $test_command_cache->getByName( 'store' );

         $html_source = stripslashes( $this->html_source ); 
        
         $html_source_parser_obj = new CTM_Test_Html_Source_Parser();
         $parsed_data = $html_source_parser_obj->parse( $html_source );

         $test_sel = new CTM_Test_Selector();
         $test_params = array( new Light_Database_Selector_Criteria( 'id', '=', $this->test_id ) );
         $tests = $test_sel->find( $test_params );
         if ( isset( $tests[0] ) ) {
            $test = $tests[0];
         }

         $test->setBaseUrl( (string) $parsed_data['baseurl'] );

         $used_variables = array();
         foreach ( $parsed_data['commands'] as $command_trinome ) {
            list( $command, $target, $value ) = $command_trinome;

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
               if ( $command_obj->id == $store_command->id && preg_match( '/^ctm_var_(.*)/', (string) $value ) ) { 
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
               } else { 
                  // Does this item use a test_param_library value?
                  if ( preg_match_all( '/\$\{(ctm_var_.*?)\}/', $target, $target_matches, PREG_SET_ORDER ) ) {
                     foreach ( $target_matches as $match ) {
                        list( $m, $m_v ) = $match;
                        $used_variables[ $m_v ] = 1;
                     }
                  }
                  if ( preg_match_all( '/\$\{(ctm_var_.*?)\}/', $value, $value_matches, PREG_SET_ORDER ) ) {
                     foreach ( $value_matches as $match ) {
                        list( $m, $m_v ) = $match;
                        $used_variables[ $m_v ] = 1;
                     }
                  }
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

         foreach ( $used_variables as $var_name => $var_used ) {
            $test_param_lib_obj = $test_param_lib_cache->getByName( $var_name );
            if ( isset( $test_param_lib_obj->id ) ) {
               // test_param_obj found... save the shit to the table.
               $test_param = new CTM_Test_Param();
               $test_param->test_id = $this->test_id;
               $test_param->test_param_library_id = $test_param_lib_obj->id;
               $test_param->save();
            }
         }

      } catch ( Exception $e ) {
      }

   }

}
