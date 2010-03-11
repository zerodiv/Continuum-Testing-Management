<?php

class Light_CommandLine_Option_Type {
   public static function TYPE_BOOLEAN() { return 'bool'; }
   public static function TYPE_STRING() { return 'string'; }
   public static function GET_ALL_TYPES() {
      return array( 
            Light_CommandLine_Option_Type::TYPE_BOOLEAN,
            Light_CommandLine_Option_Type::TYPE_STRING
      );
   }
}

class Light_CommandLine_Option {
   private $_name;
   private $_default_value;
   private $_is_required;
   private $_value;
   private $_type;

   function __construct() {
      $this->_name = '';
      $this->_default_value = '';
      $this->_is_required = 0;
      $this->_value = null;

      // we default ot string 
      $this->_type = Light_CommandLine_Option_Type::TYPE_STRING();

   }


   public function setName( $name ) {
      $this->_name = $name;
      return true;
   }

   public function getName() {
      return $this->_name;
   }

   public function setDefaultValue( $default_value ) {
      $this->_default_value = $default_value;
      return true;
   }

   public function getDefaultValue() {
      return $this->_default_value;
   }

   public function setIsRequired( $is_required ) {
      if ( $is_required == true || $is_required == false ) {
         $this->_is_required = $is_required;
         return true;
      }
      return false;
   }

   public function getIsRequired() {
      return $this->_is_required;
   }

   public function setValue( $value ) {
      $this->_value = $value;
      return true;
   }

   public function getValue() {
      return $this->_value;
   }

   public function setType( $option_type ) {
      $option_types = Light_CommandLine_Option_Type::GET_ALL_TYPES();

      if ( in_array( $option_types, $option_type ) ) {
         $this->_type = $option_type;
         return true;
      }

      return false;
   }

}
