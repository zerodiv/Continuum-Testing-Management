<?php

require_once( 'Light/Database/Factory.php' );

abstract class Light_Database_Object {
   private $_sql_id_field;
   private $_sql_table;
   private $_db_name;

   public function __construct() {

      $this->_sql_id_field = 'id';
      $this->_sql_table = null;
      $this->_db_name = null;

      $this->init();

      if ( $this->_db_name != null ) {
         throw new Exception( '_db_name: Database name is not configured' );
      }

      if ( $this->_sql_table != null ) {
         throw new Exception( '_sql_table: Sql table is not configured' );
      }

   }

   public function setSqlTable( $table_name ) {
      $this->_sql_table = $table_name;
      return true;
   }

   public function getSqlTable() {
      return $this->_sql_table;
   }

   public function setDbName( $name ) {
      $this->_db_name = $name;
      return true; 
   }

   public function getDbName() {
      return $this->_db_name;
   }

   public function init() {
      throw new Exception( 'init should be overwritten with a client side impl' );
   }

   public function getFieldNames() {
      $fields = array();
      $this_obj = new ReflectionClass( get_class( $this ) );
      $props = $this_obj->getProperties();
      foreach ( $props as $prop ) {
         $prop_name = $prop->getName();
         if ( $prop->isPublic() && isset( $hash[ $prop_name ] ) ) {
            $fields[] = $prop_name;
         }
      }
      return $fields;
   }

   public function consumeHash( $hash ) {
      $fields = $this->getFieldNames();
      foreach ( $fields as $field ) {
         $this->$field = $hash[ $field ];
      }
      return true;
   }

   public function save() {
      $fields = $this->getFieldNames();
      $upd_sql = 'UPDATE ' . $this->_sql_table . ' SET ';
      $is_first = true;
      foreach ( $fields as $field ) {
         if ( $this->_sql_id_field != $field ) {
            if ( $is_first == false ) {
               $upd_sql .= ', ';
            }
            $upd_sql .= $field . ' = ?';
            $is_first = false;
         }
      }
      $upd_sql .= ' WHERE ' . $this->_sql_id_field . ' = ?';
     
      try {
        
         $dbh = Light_Database_Factory::getDBH( $this->_db_name );

         $upd_sth = $dbh->prepare( $upd_sql );
         $field_id = 0;
         foreach ( $fields as $field ) {
            if ( $this->_sql_id_field != $field ) {
               $field_id++;
               $upd_sth->bindParam( $field_id, $this->$field );
            }
         }
         $field_id++;
         $upd_sth->bindParam( $field_id, $this->id );
         $upd_sth->execute();
      } catch ( Exception $e ) {
         return false;
      }
         
      return true;
   }

}
