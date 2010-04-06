<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Suite_BaseUrl extends Light_Database_Object {
   public $id;
   public $test_suite_id;
   public $baseurl;

   public function init() {
      $this->setSqlTable( 'test_suite_baseurl' );
      $this->setDbName( 'test' );
   }

}
