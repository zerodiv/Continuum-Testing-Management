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


class CTM_Test_Html_Source extends Light_Database_Object
{
   public $id;
   public $testId;
   public $htmlSource;

   public function init()
   {
      $this->setSqlTable('test_html_source');
      $this->setDbName('test');
   }

   public function save( CTM_User $user )
   {

      parent::save();

      // pass the user down into the save.
      $this->parseToTestCommands($user);

   }

   public function parseToTestCommands( CTM_User $user )
   {

      try {

         // if there are commands associated to this test purge them.
         $sel = new CTM_Test_Command_Selector();
         $andParams = array(
               new Light_Database_Selector_Criteria('testId', '=', $this->testId)
         );

         $commandRows = $sel->find($andParams);

         if ( count($commandRows) > 0 ) {
            foreach ( $commandRows as $commandRow ) {
               $commandRow->remove();
            }
         }

         $testCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Cache');
         $testCommandCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Selenium_Command_Cache');
         $testParamLibCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Param_Library_Cache');

         // pull out a store command so we have a id to work with.
         $storeCommand = $testCommandCache->getByName('store');

         $htmlSource = stripslashes($this->htmlSource); 
        
         $htmlSourceParserObj = new CTM_Test_Html_Source_Parser();
         $parsedData = $htmlSourceParserObj->parse($htmlSource);

         $test = $testCache->getById($this->testId);

         if ( ! isset( $test ) ) {
            throw new Exception( 'Failed to find test: ' . $this->testId );
         }

         $test->setBaseUrl( (string) $parsedData['baseurl'] );

         $used_variables = array();
         foreach ( $parsedData['commands'] as $command_trinome ) {
            list( $command, $target, $value ) = $command_trinome;

            // first lookup the slenium command object
            $command_obj = null;
            $command_obj = $testCommandCache->getByName( $command );
            
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
               if ( $command_obj->id == $storeCommand->id && preg_match( '/^ctm_var_(.*)/', (string) $value ) ) { 
                  $test_param_lib_obj = $testParamLibCache->getByName( (string) $value ); 
                  if ( ! isset( $test_param_lib_obj ) ) { 
                     $createdAt = time();
                     $test_param_lib_obj = new CTM_Test_Param_Library();
                     $test_param_lib_obj->name = (string) $value;
                     $test_param_lib_obj->createdAt = $createdAt;
                     $test_param_lib_obj->createdBy = $user->id;
                     $test_param_lib_obj->modifiedAt = $createdAt;
                     $test_param_lib_obj->modifiedBy = $user->id;
                     $test_param_lib_obj->save();

                     if ( isset( $test_param_lib_obj->id ) ) {
                        $test_param_lib_obj->setDescription( '' );
                        $test_param_lib_obj->setDefault( $target );
                     } 
                  }
               } 


               // create the test command
               $c = new CTM_Test_Command();
               $c->testId = $this->testId;
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
            $test_param_lib_obj = $testParamLibCache->getByName( $var_name );
            if ( isset( $test_param_lib_obj->id ) ) {
               // test_param_obj found... save the shit to the table.
               $test_param = new CTM_Test_Param();
               $test_param->testId = $this->testId;
               $test_param->test_param_library_id = $test_param_lib_obj->id;
               $test_param->save();
            }
         }

      } catch ( Exception $e ) {
      }

   }

}
