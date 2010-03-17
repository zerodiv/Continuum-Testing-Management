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

      if ( $this->_db_name == null ) {
         throw new Exception( '_db_name: Database name is not configured' );
      }

      if ( $this->_sql_table == null ) {
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
         if ( $prop->isPublic() ) {
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

   private function _createInsertStatement() {
      $fields = $this->getFieldNames();
      $ins_sql = 'INSERT INTO ' . $this->_sql_table . ' ( ';
      $is_first = true;
      foreach ( $fields as $field ) {
         if ( $field != $this->_sql_id_field ) {
            if ( $is_first != true ) {
               $ins_sql .= ', ';
            }
            $ins_sql .= $field;
            $is_first = false;
            }
      }
      $ins_sql .= ') VALUES ( ';
      $is_first = true;
      foreach ( $fields as $field ) {
         if ( $field != $this->_sql_id_field ) {
            if ( $is_first != true ) {
               $ins_sql .= ', ';
            }
            $ins_sql .= '?';
            $is_first = false;
            }
      }
      $ins_sql .= ')';
      return $ins_sql;
   }

   private function _createUpdateStatement() {
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
      return $upd_sql;
   }

   public function save() {
      $fields = $this->getFieldNames();

      // is this a insert ? 
      $sql = null;
      $is_insert = false;
      if ( $this->{ $this->_sql_id_field } == null ) {
         $is_insert = true;
         $sql = $this->_createInsertStatement();
      } else {
         $sql = $this->_createUpdateStatement();
      }

      try {
        
         $dbh = Light_Database_Factory::getDBH( $this->_db_name );

         // prepare the sql statement
         $sth = $dbh->prepare( $sql );

         // bind the parameters to the statement
         $field_id = 0;
         foreach ( $fields as $field ) {
            // both a insert and update skip their id fields
            if ( $this->_sql_id_field != $field ) {
               $field_id++;
               $sth->bindParam( $field_id, $this->$field );
            }
         }

         // if we are doing a update we need to add the id
         if ( $is_insert != true ) {
            $field_id++;
            $sth->bindParam( $field_id, $this->${ $this->_sql_id_field } );
         }

         // run the query
         $sth->execute();

      } catch ( Exception $e ) {
         return false;
      }
         
      return true;
   }

}
