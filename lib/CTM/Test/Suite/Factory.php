<?php

class PFL_Test_Suite_Factory {
   public $_dbh;

   function __construct() {
   }

   public function getDBH() {
      if ( isset( $this->_dbh ) ) {
         return $this->_dbh;
      }
      $this->_dbh = null;
      try {
         $this->_dbh = new PDO( PFL_Test_Suite_Factory_Config::DB_DSN(), PFL_Test_Suite_Factory_Config::DB_USERNAME(), PFL_Test_Suite_Factory_Config::DB_PASSWORD() );
      } catch ( Exception $e ) {
         return;
      }
      return $this->_dbh;
   }

}
