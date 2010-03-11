<?php

class Light_DBO {
   private $_sql_id_field;
   private $_sql_table;

   public function __construct() {
      $this->_sql_id_field = 'id';
      $this->_sql_table = '';
      $this->init();
   }

   public function setSqlTable( $table_name ) {
      $this->_sql_table = $table_name;
      return true;
   }

   public function init() {
      throw new Exception( 'init should be overwritten with a client side impl' );
   }

   public function getDBH() {
      throw new Exception( 'getDBH should be overwritten with a client side impl' );
   }

   public function consumeHash( $hash ) {
      $this_obj = new ReflectionClass( get_class( $this ) );
      $props = $this_obj->getProperties();
      foreach ( $props as $prop ) {
         $prop_name = $prop->getName();
         if ( $prop->isPublic() && isset( $hash[ $prop_name ] ) ) {
            $this->$prop_name = $hash[ $prop_name ];
         }
      }
      return true;
   }

   public function save() {
      $this_obj = new ReflectionClass( get_class( $this ) );
      $props = $this_obj->getProperties();
      $upd_sql = 'UPDATE ' . $this->_sql_table . ' SET ';
      $is_first = true;
      foreach ( $props as $prop ) {
         $prop_name = $prop->getName();
         if ( $this->_sql_id_field != $prop_name ) {
            if ( $is_first == false ) {
               $upd_sql .= ', ';
            }
            $upd_sql .= $prop_name . ' = ?';
            $is_first = false;
         }
      }
      $upd_sql .= ' WHERE ' . $this->_sql_id_field . ' = ?';
     
      try {
         $dbh = $this->getDBH();

         $upd_sth = $dbh->prepare( $upd_sql );
         $field_id = 0;
         foreach ( $props as $prop ) {
            $prop_name = $prop->getName();
            if ( $this->_sql_id_field != $prop_name ) {
               $field_id++;
               $upd_sth->bindParam( $field_id, $this->$prop_name );
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
