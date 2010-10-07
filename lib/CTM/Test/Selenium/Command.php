<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Selenium_Command extends Light_Database_Object
{
   public $id;
   public $name;

   public function init()
   {
      $this->setSqlTable('ctm_test_selenium_command');
      $this->setDbName('test');
   }

}
