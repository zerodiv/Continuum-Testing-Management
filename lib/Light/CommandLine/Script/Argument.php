<?php
class Light_CommandLine_Script_Argument {
   /**
    * The name for the commandline argument.
    * 
    * @var string
    * @access private
    */
   private $_name;

   /**
    * The description for the commandline argument.
    * 
    * @var string
    * @access private
    */
   private $_description;

   /**
    * Wether or not we require this commandline option.
    * 
    * @var boolean
    * @access private
    */
   private $_isRequired;

   /**
    * The regular expression used to validate the data for this parameter
    * 
    * @var string
    * @access private
    */
   private $_pattern;

   /**
    * The current value for the parameter.
    * 
    * @var mixed
    * @access private
    */
   private $_value;

   /**
    * Status variable for wether or not a value has been set for this parameter.
    * 
    * @var boolean
    * @access private
    */
   private $_hasValue;

   /**
    * The default value for the parameter.
    * 
    * @var mixed
    * @access private
    */
   private $_defaultValue;

   /**
    * Status variable for wether or not a default value has been set for this parameter.
    * 
    * @var mixed
    * @access private
    */
   private $_hasDefaultValue;

   /**
    * Wether or not this parameter supports multiple callings ie, --foo=baz --foo=bar 
    * 
    * @var boolean
    * @access private
    */
   private $_isMultiValue;


   /**
    * This force casts the values to a specific php type (string|int|boolean)
    * 
    * @var string
    * @access private
    */
   private $_castToType;

   function __construct( $name, $description ) {
      $this->setName( $name );
      $this->setDescription( $description );
      $this->_description     = $description;
      $this->_isRequired      = false;
      $this->_pattern         = false;
      $this->_value           = null;
      $this->_hasValue        = false;
      $this->_defaultValue    = null;
      $this->_hasDefaultValue = false;
      $this->_isMultiValue    = false;
      $this->_castToType      = 'string';
   }

   /**
    * Sets the commandline name for this parameter.
    * 
    * @param string $name 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setName( $name ) {
      if ( $name == '' ) {
         throw new Exception( 'argument name must not be empty' );
      }
      if ( ! preg_match( '/^[a-z|A-Z|0-9|\_]*$/', $name ) ) {
         throw new Exception( 'argument name must be [a-z|A-Z|0-9]: ' . $name . ' does not follow that standard.' );
      }
      $this->_name = $name;
      return $this;
   }

   /**
    * Gets the comandline name for this parameter.
    * 
    * @access public
    * @return string
    */
   public function getName() {
      return $this->_name;
   }

   /**
    * Sets the description for this parameter.
    * 
    * @param string $description 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setDescription( $description ) {
      if ( $description == '' ) {
         throw new Exception( 'description must not be empty.' );
      }
      $this->_description = $description;
      return $this;
   }

   /**
    * getDescription 
    * 
    * @access public
    * @return string
    */
   public function getDescription() {
      return $this->_description;
   }

   /**
    * setIsRequired 
    * 
    * @param boolean $required 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setIsRequired( $required ) {
      if ( $required == true || $required == false ) {
         $this->_isRequired = $required;
         return $this;
      }
      throw new Exception( 'setIsRequired requires a true or false value: ' . $required . ' provided' );
   }

   /**
    * getIsRequired
    * 
    * @access public
    * @return void
    */
   public function getIsRequired() {
      return $this->_isRequired;
   }

   /**
    * Sets the validation pattern for this parameter.
    * 
    * @param string $pattern 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setPattern( $pattern ) {
      @preg_match( $pattern, 'sometestvalue' );
      if ( preg_last_error() == PREG_NO_ERROR ) {
         // verified that the regexp is valid
         $this->_pattern = $pattern;
         return $this;
      }
      throw new Exception( 'Failed to parse pattern: ' . $pattern );
   }

   /**
    * getPattern 
    * 
    * @access public
    * @return string
    */
   public function getPattern() {
      return $this->_pattern;
   }

   /**
    * Casts a value into a php specific version of the value.
    * 
    * @param string $type 
    * @param mixed $value 
    * @access private
    * @return void
    */
   private function _castValueToType( $type, $value ) {
      if ( $type == 'string' ) {
         return (string) $value;
      }
      if ( $type == 'boolean' ) {
         // convert the string version of the value to a int
         if ( strtolower($value) == 'true' ) {
            $value = 1;
         }
         // for whatever reason string 'false' evals to true in php's type conversion (WTH?)
         if ( strtolower($value) == 'false' ) {
            $value = 0;
         }
         return (boolean) $value;
      }
      if ( $type == 'int' ) {
         return (int) $value;
      }
      throw new Exception( '_castValueToType: unsupported type: ' . $type );
   }

   /**
    * Sets the value for a parameter, if it's a array it appends it to the array of possible values.
    * 
    * @param string $value 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setValue( $value ) {
      if (!empty($this->_pattern) && is_string($value) && !preg_match($this->_pattern, $value))  {
         throw new Exception( 'value: ' . $value . ' does not match pattern: ' . $this->_pattern . ' for parameter: ' . $this->_name );
      }
      if ( $this->_isMultiValue == true ) {
         if ( $this->_value == null ) {
            $this->_value = array();
         }
         $this->_value[] = $this->_castValueToType( $this->_castToType, $value );
         $this->_hasValue = true;
      } else {
         if ( ! empty( $this->_value ) ) {
            throw new Exception( $this->getName() . ' does not accept multiple values for the parameter' );
         }
         $this->_value = $this->_castValueToType( $this->_castToType, $value );
         $this->_hasValue = true;
      }
      return $this;
   }

   /**
    * Gets the value of the parameter.
    * 
    * @access public
    * @return mixed
    */
   public function getValue() {
      return $this->_value;
   }

   /**
    * Returns if the value for this argument has been set
    * 
    * @access public
    * @return boolean
    */
   public function hasValue() {
      return $this->_hasValue;
   }

   /**
    * setDefaultValue 
    * 
    * @param mixed $value 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setDefaultValue( $value ) {
      if (!empty($this->_pattern) && is_string($value) && !preg_match($this->_pattern, $value))  {
         throw new Exception( 'value: ' . $value . ' does not match pattern: ' . $this->_pattern . ' for parameter: ' . $this->_name );
      }
      if ( $this->_isMultiValue == true ) {
         if ( $this->_defaultValue == null ) {
            $this->_defaultValue = array();
         }
         $this->_defaultValue[] = $value;
      } else {
         if ( isset( $this->_defaultValue ) ) {
            throw new Exception( $this->getName() . ' does not accept multiple values for the parameter' );
         }
         $this->_defaultValue = $value;
      }
      return $this;
   }

   /**
    * Gets the default value for the script.
    * 
    * @access public
    * @return mixed
    */
   public function getDefaultValue() {
      return $this->_defaultValue;
   }

   /**
    * Returns if the default value for this argument has been set
    * 
    * @access public
    * @return boolean
    */
   public function hasDefaultValue() {
      return $this->_hasDefaultValue;
   }

   /**
    * Flags if this parameter allows multiple use ie: --foo=bar --foo=baz --foo=barf
    * 
    * @param boolean $ismultivalue 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setIsMultiValue( $ismultivalue ) {
      if ( $ismultivalue == true || $ismultivalue == false ) {
         $this->_isMultiValue = $ismultivalue;
         return $this;
      }
      throw new Exception( 'setIsMultiValue accepts true/false: ' . $ismultivalue . ' provided.' );
   }

   /**
    * Returns the current value for isMultiValue
    * 
    * @access public
    * @return boolean
    */
   public function getIsMultiValue() {
      return $this->_isMultiValue;
   }

   /**
    * Sets how we cast the values to one of: (string|int|boolean) php types.
    * 
    * @param string $type 
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &setCastToType( $type ) {
      $acceptable_types = array( 'string', 'int', 'boolean' );
      if ( ! in_array( $type, $acceptable_types ) ) {
         throw new Exception( 'setCatToType: we support (string|int|boolean) types you provided: ' . $type );
      }
      $this->_castToType = $type;
      return $this;
   }

   /**
    * Get the current setting for what php type we are casting the values against.
    * 
    * @access public
    * @return void
    */
   public function getCastToType() {
      return $this->_castToType;
   }

}
