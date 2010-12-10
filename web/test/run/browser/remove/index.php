<?php

require_once( '../../../../../bootstrap.php' );
require_once( 'Light/Config.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Browser/Selector.php' );

class CTM_Site_Test_Run_Browser_Remove extends CTM_Site
{

   public function setupPage()
   {
      return true;
   }

   public function handleRequest()
   {
      $this->requiresAuth();

      $id = $this->getOrPost('testRunBrowserId', '');

      try {

         $testRunBrowserCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Run_Browser_Cache');

         $testRunBrowser = $testRunBrowserCache->getById($id);

         if ( isset($testRunBrowser->id) && $testRunBrowser->id > 0 ) {
            $testRunBrowser->remove();
            header('Location: ' . $this->getBaseUrl() . '/test/runs/' );
            return false;
         }

      } catch ( Exception $e ) {
         header('Location: ' . $this->getBaseUrl() . '/test/runs/');
         echo 'unable to find test run browser by id provided';
         return false;
      }

      header('Location: ' . $this->getBaseUrl() . '/test/runs/');
      return false;

   }
                           

   public function displayBody()
   {
      return true;
   }

}

$testObj = new CTM_Site_Test_Run_Browser_Remove();
$testObj->displayPage();
