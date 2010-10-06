<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Command_Target extends Light_Database_Object
{
   public $id;
   public $testCommandId;
   public $target;

   public function init()
   {
      $this->setSqlTable('ctm_test_command_target');
      $this->setDbName('test');
   }

}
