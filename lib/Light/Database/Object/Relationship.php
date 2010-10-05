<?php

class Light_Database_Object_Relationship
{
   const ONE_TO_ONE = 1;
   const ONE_TO_MANY = 2;

   private $_localName;
   private $_objectName;
   private $_sourceField;
   private $_linkingField;
   private $_type;
   private $_useCache;

   public function __construct(
         $localName,
         $objectName,
         $sourceField,
         $linkingField,
         $type,
         $useCache = false )
   {

      $this->setLocalName($localName);
      $this->setObjectName($objectName);
      $this->setSourceField($sourceField);
      $this->setLinkingField($linkingField);
      $this->setType($type);
      $this->setUseCache($useCache);

      $this->_loadObjects($objectName);

   }

   public function getLocalName()
   {
      return $this->_localName;
   }

   public function setLocalName($name)
   {
      $this->_localName = $name;
   }

   public function getObjectName()
   {
      return $this->_objectName;
   }

   public function setObjectName($object)
   {
      $this->_objectName = $object;
   }

   public function getSourceField()
   {
      return $this->_sourceField;
   }

   public function setSourceField($field)
   {
      $this->_sourceField = $field;
   }

   public function getLinkingField()
   {
      return $this->_linkingField;
   }

   public function setLinkingField($field)
   {
      $this->_linkingField = $field;
   }

   public function getType()
   {
      return $this->_type;
   }

   public function setType($type)
   {
      if ( $type != Light_Database_Object_Relationship::ONE_TO_ONE && 
           $type != Light_Database_Object_Relationship::ONE_TO_MANY ) {
         throw new Exception( 'Light_Database_Object_Relationship: unsupported relationship type- ' . $type );
      }
      $this->_type = $type;
   }

   public function setUseCache($useCache)
   {
      if ( $useCache == true ) {
         $this->_useCache = true;
      } else {
         $this->_useCache = false;
      }
   }

   public function getUseCache()
   {
      return $this->_useCache;
   }

   private function _loadObjects( $objectName )
   {
      if ( ! class_exists($objectName) ) {
         $phpFilename = str_replace('_', '/', $objectName) . '.php';
         require_once( $phpFilename );
      }
      if ( ! class_exists($objectName . '_Selector') ) {
         $phpFilename = str_replace('_', '/', $objectName) . '/Selector.php';
         require_once( $phpFilename );
      }
      if ( $this->_useCache == true && ! class_exists($objectName . '_Cache') ) {
         $phpFilename = str_replace('_', '/', $objectName) . '/Cache.php';
         require_once( $phpFilename );
      }
   }

}

