<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Run_BaseUrl extends Light_Database_Object {
   public $id;
   public $test_run_id;
   public $test_suite_id;
   public $test_id;
   public $baseurl;

   public function init() {
      $this->setSqlTable( 'test_run_baseurl' );
      $this->setDbName( 'test' );
   }

}
