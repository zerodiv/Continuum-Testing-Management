<?php

require_once( 'Light/Database/Object/Relationship.php' );

class Light_Database_Object_Relationship_Container
{
   private $_objectRelationships;

   function __construct()
   {
      $this->_objectRelationships = array();
   }

   public function add( Light_Database_Object_Relationship $rel )
   {
      $obj = $this->findByName($rel->getLocalName());
      if ( is_object($obj) ) {
         throw new Exception(
            'Your model class has a duplicated localName please ' .
            ' pick another name for: ' . 
            $rel->getLocalName()
         );
      }
      $this->_objectRelationships[] = $rel;
   }

   public function findByName( $name )
   {
      foreach ( $this->_objectRelationships as $obj ) {
         if ( $obj->getLocalName() == $name ) {
            return $obj;
         }
      }
      return null;
   }

   public function getAll()
   {
      return $this->_objectRelationships;
   }

}
