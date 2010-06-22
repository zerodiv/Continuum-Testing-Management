<?php

require_once( 'Light/Database/Factory.php' );
require_once( 'Light/Database/Object/Relationship.php' );
require_once( 'Light/Database/Object/Relationship/Container.php' );

abstract class Light_Database_Object {
   private $_sql_id_field;
   private $_sql_table;
   private $_db_name;
   private $_object_relationships;

   public function __construct() {

      $this->_sql_id_field = 'id';
      $this->_sql_table = null;
      $this->_db_name = null;
      $this->_object_relationships = new Light_Database_Object_Relationship_Container();

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

   public function addOneToOneRelationship( $localName, $objectName, $sourceField, $linkingField ) {
      $this->_object_relationships->add( 
            new Light_Database_Object_Relationship( 
               $localName, 
               $objectName, 
               $sourceField, 
               $linkingField, 
               Light_Database_Object_Relationship::ONE_TO_ONE
               )
      );
   }

   public function addOneToManyRelationship( $localName, $objectName, $sourceField, $linkingField ) {
      $this->_object_relationships->add( 
            new Light_Database_Object_Relationship( 
               $localName, 
               $objectName, 
               $sourceField, 
               $linkingField, 
               Light_Database_Object_Relationship::ONE_TO_MANY
               )
      );
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

   public function remove() {

      $sql = 'DELETE FROM ' . $this->_sql_table . ' WHERE ' . $this->_sql_id_field . ' = ?';

      try {

         $dbh = Light_Database_Factory::getDBH( $this->_db_name );

         $sth = $dbh->prepare( $sql );
         
         $id_field = $this->_sql_id_field;

         $sth->bindParam( 1, $this->$id_field );

         $sth->execute();

      } catch ( Exception $e ) {
         throw $e;
      }

      return true;

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

      // echo "sql: $sql\n";

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
               // echo 'f- ' . $field . '[' . $field_id . '] - ' . $this->$field . "\n";
               $sth->bindParam( $field_id, $this->$field );
            }
         }

         // if we are doing a update we need to add the id
         if ( $is_insert != true ) {
            $field_id++;
            $id_field = $this->_sql_id_field;
            $sth->bindParam( $field_id, $this->$id_field );
         }

         // run the query
         $sth->execute();

         // if it was a insert grab the id of the inserted item back from the db.
         if ( $is_insert == true ) { 
           
            $last_id_sth = $dbh->prepare( 'SELECT LAST_INSERT_ID() as last_id' );
            $last_id_sth->execute();

            $last_id = $last_id_sth->fetchAll( PDO::FETCH_BOTH );
            
            if ( isset( $last_id[0]['last_id'] ) ) {
               $last_id = intval( $last_id[0]['last_id'] );
            } else {
               $last_id = null;
            } 
            
            if ( $last_id != null ) {
               $id_field = $this->_sql_id_field;
               $this->$id_field = $last_id;
            }

         } // end if this was a insert

      } catch ( Exception $e ) {
         return false;
      }
         
      return true;
   }

   private function _fetchRelatedObjects( Light_Database_Object_Relationship $rel ) {
      $leftside_field = $rel->sourceField;
      if ( ! isset( $this->$leftside_field ) ) {
         return null;
      } 
      try {
         $sel_name = $rel->objectName . '_Selector';
         $sel = new $sel_name();
         $and_params = array( new Light_Database_Selector_Criteria( $rel->linkingField, '=', $this->$leftside_field ) );
         $rows = $sel->find( $and_params );
         if ( $rel->type == Light_Database_Object_Relationship::ONE_TO_ONE && isset( $rows[0] ) ) {
            return $rows[0];
         }
         if ( $rel->type == Light_Database_Object_Relationship::ONE_TO_MANY && count( $rows ) > 0 ) {
            return $rows;
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }

   public function __call( $method, $args ) {
      if ( preg_match( '/^(get|set)(.*)$/', $method, $methodPregs ) ) {
         $operand = $methodPregs[1];
         $localName = $methodPregs[2];

         $rel = $this->_object_relationships->findByName( $localName );

         //---
         // TODO: Need to decide if we are going to support setAttribute() ever. 
         // My misgivings on it at this point is the method signature issue where we might
         // need to do some kind of complicated mapping / transformation on the params as they come in.
         //---
         if ( is_object( $rel ) ) {
            if ( $rel->type == Light_Database_Object_Relationship::ONE_TO_ONE ) {
               if ( $operand == 'get' ) {
                  return $this->_fetchRelatedObjects( $rel );
               } 
            }
            if ( $rel->type == Light_Database_Object_Relationship::ONE_TO_MANY ) {
               if ( $operand == 'get' ) {
                  return $this->_fetchRelatedObjects( $rel );
               } 
            }
         }

      }
      return;
   }

   public function toXML( $writer = null ) {

      $writer_provided = false;

      if ( $writer == null ) {
         $writer = new XMLWriter();
         $writer->openMemory();
         $writer->setIndent(true);
         $writer->startDocument( '1.0', 'UTF-8' );
      } else {
         $writer_provided = true;
      }

      $writer->startElement( get_class( $this ) );
      
      $fields = $this->getFieldNames();

      foreach ( $fields as $field ) {
         $writer->writeElement( $field, $this->$field );
      }

      // get all related objects.
      $rels = $this->_object_relationships->getAll();

      foreach ( $rels as $rel ) {
         $writer->startElement( $rel->localName );

         $getter = 'get' . $rel->localName;
         $related = $this->$getter();

         if ( is_array( $related ) ) { 
            foreach ( $related as $rel_obj ) {
               $rel_obj->toXml( $writer );
            }
         } else if ( isset( $related ) ) {
            // $related->toXml( $writer );
         }

         $writer->endElement();
      }

      $writer->endElement();

      if ( $writer_provided == false ) {
         $writer->endDocument();

         // default return xml please.
         return $writer->outputMemory( true );
      }

      return null;

   }
}
