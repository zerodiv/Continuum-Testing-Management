<?php

class Light_Database_Object_Relationship {
   const ONE_TO_ONE = 1;
   const ONE_TO_MANY = 2;

   public $localName;
   public $objectName;
   public $sourceField;
   public $linkingField;
   public $type;

   public function __construct( $localName, $objectName, $sourceField, $linkingField, $type ) {

      if ( $type != Light_Database_Object_Relationship::ONE_TO_ONE && $type != Light_Database_Object_Relationship::ONE_TO_MANY ) {
         throw new Exception( 'Light_Database_Object_Relationship: unsupported relationship type- ' . $type );
      }

      $this->_loadObject( $objectName );

      $this->localName = $localName;
      $this->objectName = $objectName;
      $this->sourceField = $sourceField;
      $this->linkingField = $linkingField;
      $this->type = $type;

   }

   private function _loadObject( $objectName ) {
      if ( ! class_exists( $objectName ) ) {
         $php_filename = str_replace( '_', '/', $objectName ) . '.php';
         require_once( $php_filename );
      }
      if ( ! class_exists( $objectName . '_Selector' ) ) {
         $php_filename = str_replace( '_', '/', $objectName ) . '/Selector.php';
         require_once( $php_filename );
      }
   }

}

