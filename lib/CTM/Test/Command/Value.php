<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Command_Value extends Light_Database_Object
{
   public $id;
   public $testCommandId;
   public $value;

   public function init()
   {
      $this->setSqlTable('ctm_test_command_value');
      $this->setDbName('test');
   }

}
