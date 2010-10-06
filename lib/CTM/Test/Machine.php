<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Machine extends Light_Database_Object
{
   public $id;
   public $guid;
   public $ip;
   public $os;
   public $machineName;
   public $createdAt;
   public $lastModified;
   public $isDisabled;

   public function init()
   {
      $this->setSqlTable('ctm_test_machine');
      $this->setDbName('test');
   }

}
