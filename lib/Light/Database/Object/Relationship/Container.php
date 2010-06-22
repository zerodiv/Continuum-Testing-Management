<?php

require_once( 'Light/Database/Object/Relationship.php' );

class Light_Database_Object_Relationship_Container {
   private $_object_relationships;

   function __construct() {
      $this->_object_relationships = array();
   }

   public function add( Light_Database_Object_Relationship $rel ) {
      $obj = $this->findByName( $rel->localName );
      if ( is_object( $obj ) ) {
         throw new Exception( 'Your model class has a duplicated localName please pick another name for: ' . $rel->localName );
      }
      $this->_object_relationships[] = $rel;
   }

   public function findByName( $name ) {
      foreach ( $this->_object_relationships as $obj ) {
         if ( $obj->localName == $name ) {
            return $obj;
         }
      }
      return null;
   }

}
