<?php

require_once( 'CTM/Test/Suite.php' );
require_once( 'CTM/Test/Suite/Factory/Config.php' );

class CTM_Test_Suite_Factory {
   public $_dbh;

   function __construct() {
   }

   public function getDBH() {
      if ( isset( $this->_dbh ) ) {
         return $this->_dbh;
      }
      $this->_dbh = null;
      try {
         $this->_dbh = new PDO( CTM_Test_Suite_Factory_Config::DB_DSN(), CTM_Test_Suite_Factory_Config::DB_USERNAME(), CTM_Test_Suite_Factory_Config::DB_PASSWORD() );
      } catch ( Exception $e ) {
         return;
      }
      return $this->_dbh;
   }

   public function getAllSuites( $sort_by = null, $only_count = false, $chunk_size = 25, $offset = 1 ) {

      // acceptable sorts...
      $acceptable_fields = array( 'id', 'account_id', 'name', 'description' );

      if ( $sort_by == null ) {
         $sort_by = 'id';
      }

      $dbh = null;
      try {
         $dbh = $this->getDBH();
      } catch ( Exception $e ) {
         throw $e;
      }

      // select all the suites and apply the logic to it
   }

}
