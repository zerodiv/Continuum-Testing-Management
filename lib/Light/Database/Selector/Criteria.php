<?php

class Light_Database_Selector_Criteria
{
   private $_field;
   private $_operator;
   private $_value;

   function __construct( $field, $operator, $value )
   {
      $this->setField($field);
      $this->setOperator($operator);
      $this->setValue($value);
   }

   public function setField( $field )
   {
      $this->_field = $field;
   }

   public function getField()
   {
      return $this->_field;
   }

   public function setOperator( $operator )
   {
      $acceptableOps = array( '=', '!=', '>', '<' );
      if ( ! in_array($operator, $acceptableOps) ) {
         throw new Exception(
               'acceptableOps = ( ' . 
                  join(', ', $acceptableOps) . 
               ' ) and ' . $operator . ' was attempted' 
         );
      }
      $this->_operator = $operator;
   }

   public function getOperator()
   {
      return $this->_operator;
   }

   public function setValue( $value )
   {
      $this->_value = $value;
   }

   public function getValue()
   {
      return $this->_value;
   }
}

