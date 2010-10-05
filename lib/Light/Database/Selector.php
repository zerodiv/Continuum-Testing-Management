<?php

require_once( 'Light/Database/Connection/Factory.php' );
require_once( 'Light/Database/Object.php' );
require_once( 'Light/Database/Selector/Criteria.php' );

abstract class Light_Database_Selector
{
   private $_dbObject;
   private $_dbName;
   private $_sqlTable;
   private $_sqlFields;

   function __construct()
   {

      $this->_dbObject = null;
      $this->_dbName = null;
      $this->_sqlTable = null;
      $this->_sqlFields = null;

      $this->init();

      // we should be able to get all of the associated tables and dbs from the dbo
      $dbo = new $this->_dbObject();

      $this->_dbName = $dbo->getDbName();
      $this->_sqlTable = $dbo->getSqlTable();
      $this->_sqlFields = $dbo->getFieldNames();

   }

   public function setDbObject( $name )
   {
      $this->_dbObject = $name;
      return true;
   }

   public function getDbObject()
   {
      return $this->_dbObject;
   }

   public function init()
   {
      throw new Exception( 'This should be overridenn with a local implementation' );
   }

   public function find(
         $andCriteria = array(),
         $orCriteria = array(),
         $fieldOrder = array(),
         $limit = null,
         $offset = null,
         $onlyCount = false,
         $hydrate = true )
   {
      // create the sql statement for this op.
      $sql = 'SELECT ';

      if ( $onlyCount == true ) {
         $sql .= ' COUNT(*) ';
      } { 
         // add the fields wanted
         $sql .= join(', ', $this->_sqlFields);
      }

      // add the target table
      $sql .= ' FROM ' . $this->_sqlTable;

      if ( count($andCriteria) > 0 || count($orCriteria) > 0 ) {
         $sql .= ' WHERE ';
      }

      if ( count($andCriteria) > 0 ) {
         $isFirst = true;
         foreach ( $andCriteria as $andCriterion ) {
            if ( $isFirst == false ) {
               $sql .= ' AND ';
            }
            $sql .= ' ' . $andCriterion->getField();
            $sql .= ' ' . $andCriterion->getOperator();
            $sql .= ' ? ';
            $isFirst = false;
         }
      }
      
      if ( count($orCriteria) > 0 ) {

          if (count($andCriteria) > 0) {
                $sql .= ' AND ';
          }

         $isFirst = true;
         $sql .= '(';
         foreach ( $orCriteria as $orCriterion ) {
            if ( $isFirst == false ) {
               $sql .= ' OR ';
            }
            $sql .= ' ' . $orCriterion->getField();
            $sql .= ' ' . $orCriterion->getOperator();
            $sql .= ' ? ';
            $isFirst = false;
         }
         $sql .= ')';
      }

      if ( count($fieldOrder) > 0 ) {
         $sql .= ' ORDER BY ';
         $sql .= join(',', $fieldOrder);
      }

      // echo "sql: $sql\n";

      try {
         $dbh = Light_Database_Connection_Factory::getDBH($this->_dbName);

         $sth = $dbh->prepare($sql);

         // bind params..
         $bindId = 1;

         if ( count($andCriteria) > 0 ) {
            foreach ( $andCriteria as $andCriterion ) { 
               // echo "bind(and): $bindId " . $andCriterion->getValue() . "\n";
               $sth->bindParam($bindId, $andCriterion->getValue());
               $bindId++;
            }
         }
         
         if ( count($orCriteria) > 0 ) {
            foreach ( $orCriteria as $orCriterion ) { 
               // echo "bind(or): $bindId " . $orCriterion->getValue() . "\n";
               $sth->bindParam($bindId, $orCriterion->getValue());
               $bindId++;
            }
         }

         $sth->execute();

         // fetch the rows
         $rows = array();
         while ($hash = $sth->fetch(PDO::FETCH_ASSOC)) {
            if ( $hydrate == true ) {
               $obj = new $this->_dbObject();
               $obj->consumeHash($hash);
               $rows[] = $obj;
            } else {
               $rows[] = $hash;
            }
         }

         return $rows;

      } catch ( Exception $e ) {
         print_r($e);
         throw $e;
      }
   }

    public function lock()
    {
        $dbh = Light_Database_Connection_Factory::getDBH($this->_dbName);
        $dbh->exec("LOCK TABLES {$this->_sqlTable} WRITE");
    }

    public function unlock()
    {
        $dbh = Light_Database_Connection_Factory::getDBH($this->_dbName);
        $dbh->exec("UNLOCK TABLES");
    }

}
