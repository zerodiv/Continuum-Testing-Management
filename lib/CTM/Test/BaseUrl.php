<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_BaseUrl extends Light_Database_Object {
   public $id;
   public $test_id;
   public $baseurl;

   public function init() {
      $this->setSqlTable( 'test_baseurl' );
      $this->setDbName( 'test' );
   }

}
