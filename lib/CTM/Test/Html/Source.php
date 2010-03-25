<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test_Html_Source extends Light_Database_Object {
   public $id;
   public $test_id;
   public $html_source;

   public function init() {
      $this->setSqlTable( 'test_html_source' );
      $this->setDbName( 'test' );
   }

}
