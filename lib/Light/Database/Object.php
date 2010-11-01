<?php

require_once( 'Light/Database/Connection/Factory.php' );
require_once( 'Light/Database/Object/Relationship.php' );
require_once( 'Light/Database/Object/Relationship/Container.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );

abstract class Light_Database_Object
{
   private $_sqlIdField;
   private $_sqlTable;
   private $_dbName;
   private $_objectRelationships;

   public function __construct()
   {

      $this->_sqlIdField = 'id';
      $this->_sqlTable = null;
      $this->_dbName = null;
      $this->_objectRelationships = new Light_Database_Object_Relationship_Container();

      $this->init();

      if ( $this->_dbName == null ) {
         throw new Exception( '_dbName: Database name is not configured' );
      }

      if ( $this->_sqlTable == null ) {
         throw new Exception( '_sqlTable: Sql table is not configured' );
      }

   }

   public function setSqlTable( $tableName )
   {
      $this->_sqlTable = $tableName;
      return true;
   }

   public function getSqlTable()
   {
      return $this->_sqlTable;
   }

   public function setDbName( $name )
   {
      $this->_dbName = $name;
      return true; 
   }

   public function getDbName()
   {
      return $this->_dbName;
   }

   public function setIdField( $fieldName )
   {
      $this->_sqlIdField = $fieldName;
      return true;
   }

   public function getIdField()
   {
      return $this->_sqlIdField;
   }

   public function addOneToOneRelationship( $localName, $objectName, $sourceField, $linkingField, $useCache = false )
   {
      $this->_objectRelationships->add(
          new Light_Database_Object_Relationship( 
               $localName, 
               $objectName, 
               $sourceField, 
               $linkingField, 
               Light_Database_Object_Relationship::ONE_TO_ONE,
               $useCache
          )
      );
   }

   public function addOneToManyRelationship( $localName, $objectName, $sourceField, $linkingField )
   {
      $this->_objectRelationships->add(
          new Light_Database_Object_Relationship( 
               $localName, 
               $objectName, 
               $sourceField, 
               $linkingField, 
               Light_Database_Object_Relationship::ONE_TO_MANY,
               false
          )
      );
   }

   public function init()
   {
      throw new Exception( 'init should be overwritten with a client side impl' );
   }

   public function getFieldNames()
   {
      $fields = array();
      $thisObj = new ReflectionClass(get_class($this));
      $props = $thisObj->getProperties();
      foreach ( $props as $prop ) {
         $propName = $prop->getName();
         if ( $prop->isPublic() ) {
            $fields[] = $propName;
         }
      }
      return $fields;
   }

   public function consumeHash( $hash )
   {
      $fields = $this->getFieldNames();
      foreach ( $fields as $field ) {
         $this->$field = $hash[ $field ];
      }
      return true;
   }

   private function _createInsertStatement()
   {
      $fields = $this->getFieldNames();
      $insSql = 'INSERT INTO ' . $this->_sqlTable . ' ( ';
      $isFirst = true;
      foreach ( $fields as $field ) {
         if ( $field != $this->_sqlIdField ) {
            if ( $isFirst != true ) {
               $insSql .= ', ';
            }
            $insSql .= $field;
            $isFirst = false;
         }
      }
      $insSql .= ') VALUES ( ';
      $isFirst = true;
      foreach ( $fields as $field ) {
         if ( $field != $this->_sqlIdField ) {
            if ( $isFirst != true ) {
               $insSql .= ', ';
            }
            $insSql .= '?';
            $isFirst = false;
         }
      }
      $insSql .= ')';
      return $insSql;
   }

   private function _createUpdateStatement()
   {
      $fields = $this->getFieldNames();
      $updSql = 'UPDATE ' . $this->_sqlTable . ' SET ';
      $isFirst = true;
      foreach ( $fields as $field ) {
         if ( $this->_sqlIdField != $field ) {
            if ( $isFirst == false ) {
               $updSql .= ', ';
            }
            $updSql .= $field . ' = ?';
            $isFirst = false;
         }
      }
      $updSql .= ' WHERE ' . $this->_sqlIdField . ' = ?';
      return $updSql;
   }

   public function remove()
   {

      $sql = 'DELETE FROM ' . $this->_sqlTable . ' WHERE ' . $this->_sqlIdField . ' = ?';

      try {

         $dbh = Light_Database_Connection_Factory::getDBH($this->_dbName);

         $sth = $dbh->prepare($sql);
         
         $idField = $this->_sqlIdField;

         $sth->bindParam(1, $this->$idField);

         $sth->execute();

      } catch ( Exception $e ) {
         throw $e;
      }

      return true;

   }

   public function save()
   {
      $fields = $this->getFieldNames();

      // is this a insert ? 
      $sql = null;
      $isInsert = false;
      if ( $this->{ $this->_sqlIdField } == null ) {
         $isInsert = true;
         $sql = $this->_createInsertStatement();
      } else {
         $sql = $this->_createUpdateStatement();
      }

      // echo "sql: $sql\n";

      try {
        
         $dbh = Light_Database_Connection_Factory::getDBH($this->_dbName);

         // prepare the sql statement
         $sth = $dbh->prepare($sql);

         // bind the parameters to the statement
         $fieldId = 0;
         foreach ( $fields as $field ) {
            // both a insert and update skip their id fields
            if ( $this->_sqlIdField != $field ) {
               $fieldId++;
               // echo 'f- ' . $field . '[' . $fieldId . '] - ' . $this->$field . "\n";
               $sth->bindParam($fieldId, $this->$field);
            }
         }

         // if we are doing a update we need to add the id
         if ( $isInsert != true ) {
            $fieldId++;
            $idField = $this->_sqlIdField;
            $sth->bindParam($fieldId, $this->$idField);
         }

         // run the query
         $sth->execute();

         // if it was a insert grab the id of the inserted item back from the db.
         if ( $isInsert == true ) { 
           
            $lastIdSth = $dbh->prepare('SELECT LAST_INSERT_ID() as last_id');
            $lastIdSth->execute();

            $lastId = $lastIdSth->fetchAll(PDO::FETCH_BOTH);
            
            if ( isset( $lastId[0]['last_id'] ) ) {
               $lastId = intval($lastId[0]['last_id']);
            } else {
               $lastId = null;
            } 
            
            if ( $lastId != null ) {
               $idField = $this->_sqlIdField;
               $this->$idField = $lastId;
            }

         } // end if this was a insert

      } catch ( Exception $e ) {
         return false;
      }
         
      return true;
   }

   private function _fetchRelatedObjects( Light_Database_Object_Relationship $rel )
   {
      $leftsideField = $rel->getSourceField();
      if ( ! isset( $this->$leftsideField ) ) {
         return null;
      }

      if ( $rel->getUseCache() == true ) {
         $getFunction = 'getBy' . ucfirst($rel->getLinkingField());
         $cacheName = $rel->getObjectName() . '_Cache';
         $cacheObject = Light_Database_Object_Cache_Factory::factory($cacheName);
         return $cacheObject->$getFunction( $this->$leftsideField );
      }

      try {
         $selName = $rel->getObjectName() . '_Selector';
         $sel = new $selName();
         /*
            echo 
               "selName: $selName " .
               " linking_field: " .  $rel->getLinkingField() . 
               " leftside($leftsideField): " . $this->$leftsideField . 
            "\n";
         */

         $andParams = array(
               new Light_Database_Selector_Criteria(
                  $rel->getLinkingField(),
                  '=',
                  $this->$leftsideField
               )
         );

         // Debugging joins.
         // print_r( $andParams );

         $rows = $sel->find($andParams);
         if ( $rel->getType() == Light_Database_Object_Relationship::ONE_TO_ONE && isset($rows[0]) ) {
            return $rows[0];
         }
         if ( $rel->getType() == Light_Database_Object_Relationship::ONE_TO_MANY && count($rows) > 0 ) {
            return $rows;
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }

   public function __call( $method, $args )
   {
      if ( preg_match('/^(get|set)(.*)$/', $method, $methodPregs) ) {
         $operand = $methodPregs[1];
         $localName = $methodPregs[2];

         $rel = $this->_objectRelationships->findByName($localName);

         //---
         // TODO: Need to decide if we are going to support setAttribute() ever. 
         // My misgivings on it at this point is the method signature issue where we might
         // need to do some kind of complicated mapping / transformation on the params as they come in.
         //---
         if ( is_object($rel) ) {
            if ( $rel->getType() == Light_Database_Object_Relationship::ONE_TO_ONE ) {
               if ( $operand == 'get' ) {
                  return $this->_fetchRelatedObjects($rel);
               } 
            }
            if ( $rel->getType() == Light_Database_Object_Relationship::ONE_TO_MANY ) {
               if ( $operand == 'get' ) {
                  return $this->_fetchRelatedObjects($rel);
               } 
            }
         }

      }
      throw new Exception( 'Failed to find method: ' . $method );
      return;
   }

   public function toXML( $writer = null )
   {

      $writerProvided = false;

      if ( $writer == null ) {
         $writer = new XMLWriter();
         $writer->openMemory();
         $writer->setIndent(true);
         $writer->startDocument('1.0', 'UTF-8');
      } else {
         $writerProvided = true;
      }

      $writer->startElement(get_class($this));
      
      $fields = $this->getFieldNames();

      foreach ( $fields as $field ) {
         $writer->writeElement($field, $this->$field);
      }

      // get all related objects.
      $rels = $this->_objectRelationships->getAll();

      foreach ( $rels as $rel ) {

         $getter = 'get' . $rel->getLocalName();
         $related = $this->$getter();

         if ( is_array($related) ) { 
            // Open up a plural relationship entry in the xml.
            $writer->startElement($rel->getLocalName() . 's');
            foreach ( $related as $relObj ) {
               $relObj->toXml($writer);
            }
            $writer->endElement();
         } else if ( isset($related) ) {
            $related->toXml($writer);
         }

      }

      $writer->endElement();

      if ( $writerProvided == false ) {
         $writer->endDocument();

         // default return xml please.
         return $writer->outputMemory(true);
      }

      return null;

   }
}
