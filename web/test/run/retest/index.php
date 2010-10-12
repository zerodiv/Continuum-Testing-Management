<?php

require_once( '../../../../bootstrap.php' );
require_once( 'Light/Config.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );

class CTM_Site_Test_Run_Retest extends CTM_Site
{

   public function setupPage()
   {
      return true;
   }

   public function handleRequest()
   {

      $this->requiresAuth();

      $id = $this->getOrPost('testRunId', '');

      try {

         $testRunStateCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Run_State_Cache');
         $testRunCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Run_Cache');

         $testRun = $testRunCache->getById($id);
         $step4State = $testRunStateCache->getByName('step4');

         if ( isset($testRun->id) && $testRun->id > 0 ) {
            $testRun->testRunStateId = $step4State->id;
            $testRun->save();
            header('Location: ' . $this->getBaseUrl() . '/test/run/add/step4/?id=' . $id);

         }

      } catch ( Exception $e ) {
         header('Location: ' . $this->getBaseUrl() . '/test/runs/');
         echo 'unable to find test run by id provided';
         return false;
      }

   }
                           

   public function displayBody()
   {
      return true;
   }

}

$testObj = new CTM_Site_Test_Run_Retest();
$testObj->displayPage();
