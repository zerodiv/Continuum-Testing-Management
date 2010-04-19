<?php

class Light_CommandLine_Option {
   private $_name;
   private $_default_value;
   private $_is_required;
   private $_value;

   function __construct( $name, $default_value = '', $is_required = false ) {
      $this->_name = $name;
      $this->_default_value = $default_value;
      $this->_is_required = $is_required;
      $this->_value = null;
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

}
