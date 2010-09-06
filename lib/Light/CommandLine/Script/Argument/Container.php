<?php

require_once( 'Light/CommandLine/Script/Argument.php' );

class Light_CommandLine_Script_Argument_Container {
   /**
    * Array of arguments 
    * 
    * @var Light_CommandLine_Script_Argument[]
    * @access private
    */
   private $_arguments;

   /**
    * __construct 
    * 
    * @access protected
    * @return void
    */
   function __construct() {
      $this->_arguments = array();
   }

   /**
    * Add a simple string argument to the commandline container
    * 
    * @param mixed $name 
    * @param mixed $description
    * @param boolean $required
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &addStringArgument( $name, $description, $required = false ) {
      $arg = new Light_CommandLine_Script_Argument( $name, $description );
      $this->addArgument( $arg );
      $arg->setIsRequired($required);
      return $arg;
   }

   /**
    * Add a boolean argument to the commandline container
    * 
    * @param mixed $name 
    * @param mixed $description
    * @param boolean $required
    * @access public
    * @return Light_CommandLine_Script_Argument
    */
   public function &addBooleanArgument( $name, $description, $required = false ) {
      $arg = new Light_CommandLine_Script_Argument( $name, $description );
      $arg->setPattern('/^TRUE|true|1|FALSE|false|0$/');
      $arg->setDefaultValue(false);
      $arg->setCastToType('boolean');
      $arg->setIsRequired($required);
      $this->addArgument( $arg );
      return $arg;
   }

   /**
    * Adds an integer argument
    *
    * @param string $name
    * @param string $description
    * @param boolean $required
    * @return <type> 
    */
   public function &addIntegerArgument( $name, $description, $required = false ) {
      $arg = new Light_CommandLine_Script_Argument( $name, $description );
      $arg->setPattern('/^\d+$/');
      $arg->setDefaultValue(null);
      $arg->setCastToType('int');
      $arg->setIsRequired($required);
      $this->addArgument( $arg );
      return $arg;
   }

   /**
    * Adds a argument to the avaliable commandline arguments for this script
    * 
    * @param Light_CommandLine_Script_Argument $arg 
    * @access public
    * @return Light_CommandLine_Script_Argument_Container
    */
   public function &addArgument(Light_CommandLine_Script_Argument &$arg) {
      $this->_arguments[] = $arg;
      return $this;
   }

   /**
    * Returns an Light_CommandLine_Script_Argument object off the script's stack by name.
    *
    * @param string $arg_name
    * @throws Exception
    * @return Light_CommandLine_Script_Argument
    */
   public function &getArgument( $arg_name ) {
      foreach ( $this->_arguments as &$arg ) {
         if ( $arg->getName() == $arg_name ) {
            return $arg;
         }
      }
      throw new Exception('Unknown argument `' . $arg_name . '` specified.');
   }

   /**
    * Overwrites a argument with a new value for it. 
    * 
    * @param Light_CommandLine_Script_Argument $arg 
    * @access protected
    * @return Light_CommandLine_Script_Argument_Container
    */
   public function &setArgument(Light_CommandLine_Script_Argument $new_arg ) {
      foreach ( $this->_arguments as &$arg ) {
         if ( $arg->getName() == $new_arg->getName() ) {
            $arg = $new_arg;
            return $this;
         }
      }
      return $this;
   }

   /**
    * Parses commandline arguments assumes $_SERVER['argv'] unless passed in
    * 
    * @access public
    * @return array( rv, errorTextArray )
    */
   public function parseArguments( $script, $args = null ) {

      if ( $args == null || count( $args ) == 0 ) {
         $args = $_SERVER['argv'];
      }
      
      // fetch the value for the arguments for our scripts out of the arg array.
      $hadErrors = false;
      $errorText = array();
      
      foreach ($this->_arguments as &$arg) {
         $isStr = '--' . $arg->getName();
         $isNotStr = '--no-' . $arg->getName();
         $regex = '/^--' . $arg->getName() . '="?(.+)?"?$/';

         foreach ($args as $t_arg) {
            $matches = null; 
            try {
               if ( $isStr == $t_arg ) {
                  $arg->setValue( true ); 
               } else if ( $isNotStr == $t_arg ) {
                  $arg->setValue( false );
               } else if (preg_match($regex, $t_arg, $matches)) {
                  if ( isset( $matches[1] ) ) {
                     $t_arg_values = explode( ',', $matches[1]);
                     foreach ( $t_arg_values as $t_arg_value ) {
                        $arg->setValue($t_arg_value);
                     }
                  } else {
                     $arg->setValue( null );
                  }
               }
            } catch (Exception $ex)  {
               $errorText[] = 'Improperly formatted argument for `' . $arg->getName() . '`.' . "\n";
               $hadErrors = true;
            }
         }

         if ( ! $arg->hasValue() && $arg->hasDefaultValue() ) {
            $arg->setValue( $arg->getDefaultValue() );
         }

         if ( $arg->getIsRequired() && ! $arg->hasValue() ) {
            $errorText[] = '--' . $arg->getName() . ' is required';
            $hadErrors = true;
         }

      }

      if ( $hadErrors == true ) {
         $this->usage( $script, $errorText );
      }

    }

   /**
    * Cleans up regexp patterns for display in the usage()
    * 
    * @param mixed $patt 
    * @access private
    * @return void
    */
   private function prettyPrintPattern( $patt ) {
      $patt = preg_replace( '/^\//', '', $patt );
      $patt = preg_replace( '/^\^/', '', $patt );
      $patt = preg_replace( '/\/$/', '', $patt );
      $patt = preg_replace( '/\$$/', '', $patt );
      return $patt;
   }

   /**
    * Prints out parameter usage for a given argument stack
    *
    * @param array $error_message
    */
   public function usage(Light_CommandLine_Script $script, $error_message = array() ) {

      if ($error_message != '') {
         $script->message('Error: ' );
         foreach ( $error_message as $error_line ) {
            $script->message($error_line);
         }
      }

      $script->message('');
      $script->message('Usage: ');
      
      $cmd_line = $script->getScriptName();
      
      foreach ($this->_arguments as $arg) { 
         $cmd_line .= ' '; 
         
         if ($arg->getIsRequired() != true) {
            $cmd_line .= '[';
         } 
        
         $cmd_line .= '--' . $arg->getName() . '=' . $this->prettyPrintPattern( $arg->getPattern() );
         
         if ($arg->getIsRequired() != true) {
            $cmd_line .= ']';
         } 
         
         if ($arg->getDefaultValue() !== NULL) {
            $cmd_line .= '(' . $arg->getDefaultValue() . ')';
         } 
      
      } 
      
      $script->message($cmd_line); 
      $script->message('');
      $script->message('Parameters: ' );
      
      foreach ( $this->_arguments as $arg ) {
         $script->message( '   --' . $arg->getName() . '=' . $this->prettyPrintPattern( $arg->getPattern() ) );
         $script->message( '     Description: ' . $arg->getDescription() );
      }

      $script->message( '' );

      if ( count( $error_message ) > 0 ) {
         $script->done(255);
      } else {
         $script->done(0);
      }

   }

}
