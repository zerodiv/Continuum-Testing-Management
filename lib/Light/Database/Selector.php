<?php

require_once( 'Light/Database/Factory.php' );
require_once( 'Light/Database/Object.php' );

class Light_Database_Selector_Criteria {
   private $_field;
   private $_operator;
   private $_value;
   function __construct( $field, $operator, $value ) {
      $this->setField( $field );
      $this->setOperator( $operator );
      $this->setValue( $value );
   }
   public function setField( $field ) {
      $this->_field = $field;
   }
   public function getField() {
      return $this->_field;
   }
   public function setOperator( $operator ) {
      $acceptable_ops = array( '=', '!=', '>', '<' );
      if ( ! in_array( $operator, $acceptable_ops ) ) {
         throw new Exception( 'acceptable_ops = ( ' . join( ', ', $acceptable_ops ) . ' ) and ' . $operator . ' was attempted' );
      }
      $this->_operator = $operator;
   }
   public function getOperator() {
      return $this->_operator;
   }
   public function setValue( $value ) {
      $this->_value = $value;
   }
   public function getValue() {
      return $this->_value;
   }
}

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

   public function find( $and_criteria = array(), $or_criteria = array(), $field_order = array(), $limit = null, $offset = null, $only_count = false, $hydrate = true ) {
      // create the sql statement for this op.
      $sql = 'SELECT ';

      if ( $only_count == true ) {
         $sql .= ' COUNT(*) ';
      } { 
         // add the fields wanted
         $sql .= join( ', ', $this->_sql_fields );
      }

      // add the target table
      $sql .= ' FROM ' . $this->_sql_table;

      if ( count( $and_criteria ) > 0 || count( $or_criteria ) > 0 ) {
         $sql .= ' WHERE ';
      }

      if ( count( $and_criteria ) > 0 ) {
         $is_first = true;
         foreach ( $and_criteria as $and_criterion ) {
            if ( $is_first == false ) {
               $sql .= ' AND ';
            }
            $sql .= ' ' . $and_criterion->getField();
            $sql .= ' ' . $and_criterion->getOperator();
            $sql .= ' ? ';
            $is_first = false;
         }
      }
      
      if ( count( $or_criteria ) > 0 ) {
         $is_first = true;
         foreach ( $or_criteria as $or_criterion ) {
            if ( $is_first == false ) {
               $sql .= ' AND ';
            }
            $sql .= ' ' . $or_criterion->getField();
            $sql .= ' ' . $or_criterion->getOperator();
            $sql .= ' ? ';
            $is_first = false;
         }
      }

      if ( count( $field_order ) > 0 ) {
         $sql .= ' ORDER BY ';
         $sql .= join( ',', $field_order );
      }

      // echo "sql: $sql\n";

      try {
         $dbh = Light_Database_Factory::getDBH( $this->_db_name );

         $sth = $dbh->prepare( $sql );

         // bind params..
         $bind_id = 1;

         if ( count( $and_criteria ) > 0 ) {
            foreach ( $and_criteria as $and_criterion ) { 
               // echo "bind(and): $bind_id " . $and_criterion->getValue() . "\n";
               $sth->bindParam( $bind_id, $and_criterion->getValue() );
               $bind_id++;
            }
         }
         
         if ( count( $or_criteria ) > 0 ) {
            foreach ( $or_criteria as $or_criterion ) { 
               // echo "bind(or): $bind_id " . $or_criterion->getValue() . "\n";
               $sth->bindParam( $bind_id, $or_criterion->getValue() );
               $bind_id++;
            }
         }

         $sth->execute();

         // fetch the rows
         $rows = array();
         while(  $hash = $sth->fetch( PDO::FETCH_ASSOC ) ) {
            if ( $hydrate == true ) {
               $obj = new $this->_db_object();
               $obj->consumeHash( $hash );
               $rows[] = $obj;
            } else {
               $rows[] = $hash;
            }
         }

         return $rows;

      } catch ( Exception $e ) {
         print_r( $e );
         throw $e;
      }
   }

    public function lock()
    {
        $dbh = Light_Database_Factory::getDBH( $this->_db_name );
        $dbh->exec("LOCK TABLES {$this->_sql_table} WRITE");
    }

    public function unlock()
    {
        $dbh = Light_Database_Factory::getDBH( $this->_db_name );
        $dbh->exec("UNLOCK TABLES");
    }

}
