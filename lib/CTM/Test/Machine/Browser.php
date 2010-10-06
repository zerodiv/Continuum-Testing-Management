<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Machine_Browser extends Light_Database_Object
{
   public $id;
   public $testMachineId;
   public $testBrowserId;
   public $isAvailable;
   public $lastSeen;

   public function init()
   {
      $this->setSqlTable('ctm_test_machine_browser');
      $this->setDbName('test');
   }

}
