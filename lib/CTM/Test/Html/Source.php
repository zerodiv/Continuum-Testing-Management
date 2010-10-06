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
      $this->setSqlTable('ctm_test_html_source');
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

         $test->setBaseUrl((string) $parsedData['baseurl']);

         $usedVariables = array();
         foreach ( $parsedData['commands'] as $commandTrinome ) {
            list( $command, $target, $value ) = $commandTrinome;

            // first lookup the slenium command object
            $commandObj = null;
            $commandObj = $testCommandCache->getByName($command);
            
            if ( ! isset( $commandObj ) ) {
               // add it to the database.
               $commandObj = new CTM_Test_Selenium_Command();
               $commandObj->name = $command;
               $commandObj->save();
            } 
            
            // if they have a command
            if ( isset( $commandObj ) && $commandObj->id > 0 ) {
               // try to pull up the test lib object if available.
               // we only care about input_variables at this time, output variables are moot
               // see if there is a library item for this value.
               $testParamLibObj = null;
               if ( $commandObj->id == $storeCommand->id && preg_match('/^ctm_var_(.*)/', (string) $value) ) { 
                  $testParamLibObj = $testParamLibCache->getByName((string) $value); 
                  if ( ! isset( $testParamLibObj ) ) { 
                     $createdAt = time();
                     $testParamLibObj = new CTM_Test_Param_Library();
                     $testParamLibObj->name = (string) $value;
                     $testParamLibObj->createdAt = $createdAt;
                     $testParamLibObj->createdBy = $user->id;
                     $testParamLibObj->modifiedAt = $createdAt;
                     $testParamLibObj->modifiedBy = $user->id;
                     $testParamLibObj->save();

                     if ( isset( $testParamLibObj->id ) ) {
                        $testParamLibObj->setDescription('');
                        $testParamLibObj->setDefault($target);
                     } 
                  }
               } 


               // create the test command
               $c = new CTM_Test_Command();
               $c->testId = $this->testId;
               $c->testSeleniumCommandId = $commandObj->id;
               $c->testParamLibraryId = 0;
               
               if ( isset( $testParamLibObj->id ) ) {
                  $c->testParamLibraryId = $testParamLibObj->id;
               } else { 
                  // Does this item use a test_param_library value?
                  if ( preg_match_all('/\$\{(ctm_var_.*?)\}/', $target, $targetMatches, PREG_SET_ORDER) ) {
                     foreach ( $targetMatches as $match ) {
                        list( $m, $mV ) = $match;
                        $usedVariables[ $mV ] = 1;
                     }
                  }
                  if ( preg_match_all('/\$\{(ctm_var_.*?)\}/', $value, $valueMatches, PREG_SET_ORDER) ) {
                     foreach ( $valueMatches as $match ) {
                        list( $m, $mV ) = $match;
                        $usedVariables[ $mV ] = 1;
                     }
                  }
               }
              

               $c->save(); 
               
               if ( isset( $c->id ) && $c->id > 0 ) {
                  $c->setTarget($target);
                  $c->setValue($value);
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

         foreach ( $usedVariables as $varName => $varUsed ) {
            $testParamLibObj = $testParamLibCache->getByName($varName);
            if ( isset( $testParamLibObj->id ) ) {
               // test_param_obj found... save the shit to the table.
               $testParam = new CTM_Test_Param();
               $testParam->testId = $this->testId;
               $testParam->testParamLibraryId = $testParamLibObj->id;
               $testParam->save();
            }
         }

      } catch ( Exception $e ) {
      }

   }

}
