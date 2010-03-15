<?php

require_once( 'Light/Database/Factory.php' );
require_once( 'Light/Database/Object.php' );

abstract class Light_Database_Selector {
   private $_db_object;
   private $_db_name;
   private $_sql_table;
   private $_sql_fields;

   function __construct() {

      $this->_db_object = null;
      $this->_db_name = null;
      $this->_sql_table = null;
      $this->_sql_fields = null;

      $this->init();

      // we should be able to get all of the associated tables and dbs from the dbo
      $dbo = new $this->_db_object();

      $this->_db_name = $dbo->getDbName();
      $this->_sql_table = $dbo->getSqlTable();
      $this->_sql_fields = $dbo->getFieldNames();

   }

   public function setDbObject( $name ) {
      $this->_db_object = $name;
      return true;
   }

   public function getDbObject() {
      return $this->_db_object;
   }

   public function init() {
      throw new Exception( 'This should be overridenn with a local implementation' );
   }

   // TODO:
   // - field choices
   // - order choices
   // - hydrate or no
   // - only count
   public function find() {
      // given a database object find all the associated rows for it.

      // create the sql statement for this op.
      $sql = 'SELECT ';

      // add the fields wanted
      $sql .= join( ', ', $this->_sql_fields );

      // add the target table
      $sql .= ' FROM ' . $this->_sql_table;

      // TODO:
      // - apply where
      // - apply order
      try {
         $dbh = Light_Database_Factory::getDBH( $this->_db_name );
         $sel_sth = $dbh->prepare( $sql );
         $sel_sth->execute();

         // fetch the rows
         $rows = array();
         while(  $hash = $sel_sth->fetch( PDO::FETCH_ASSOC ) ) {
            $obj = new $this->_db_object();
            $obj->consumeHash( $hash );
            $rows[] = $obj;
         }

         return $rows;

      } catch ( Exception $e ) {
         throw $e;
      }
   }

}
