<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_Log extends Light_Database_Object
{
    
   public $id;
   public $testRunBrowserId;
   public $seleniumLog;
   public $runLog;
   public $duration;
   public $createdAt;

   public function init()
   {
      $this->setSqlTable('ctm_test_run_log');
      $this->setDbName('test');
   }

}
