<?php

require_once( 'Light/CommandLine/Script/Argument.php' );

/**
 * Light_CommandLine_Script_Argument_Container 
 * 
 * @package Platform
 * @version $Id: $
 * @copyright  Adicio 
 * @author $Author: $ 
 * @license 
 */
class Light_CommandLine_Script_Argument_Container
{
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
   function __construct()
   {
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
   public function &addStringArgument( $name, $description, $required = false )
   {
      $arg = new Light_CommandLine_Script_Argument( $name, $description );
      $this->addArgument($arg);
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
   public function &addBooleanArgument( $name, $description, $required = false )
   {
      $arg = new Light_CommandLine_Script_Argument( $name, $description );
      $arg->setPattern('/^TRUE|true|1|FALSE|false|0$/');
      $arg->setDefaultValue(false);
      $arg->setCastToType('boolean');
      $arg->setIsRequired($required);
      $this->addArgument($arg);
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
   public function &addIntegerArgument( $name, $description, $required = false )
   {
      $arg = new Light_CommandLine_Script_Argument( $name, $description );
      $arg->setPattern('/^\d+$/');
      $arg->setDefaultValue(null);
      $arg->setCastToType('int');
      $arg->setIsRequired($required);
      $this->addArgument($arg);
      return $arg;
   }

   /**
    * Adds a argument to the avaliable commandline arguments for this script
    * 
    * @param Light_CommandLine_Script_Argument $arg 
    * @access public
    * @return Light_CommandLine_Script_Argument_Container
    */
   public function &addArgument(Light_CommandLine_Script_Argument &$arg)
   {
      $this->_arguments[] = $arg;
      return $this;
   }

   /**
    * Returns an Light_CommandLine_Script_Argument object off the script's stack by name.
    *
    * @param string $argName
    * @throws Exception
    * @return Light_CommandLine_Script_Argument
    */
   public function &getArgument( $argName )
   {
      foreach ( $this->_arguments as &$arg ) {
         if ( $arg->getName() == $argName ) {
            return $arg;
         }
      }
      throw new Exception('Unknown argument `' . $argName . '` specified.');
   }

   /**
    * Overwrites a argument with a new value for it. 
    * 
    * @param Light_CommandLine_Script_Argument $arg 
    * @access protected
    * @return Light_CommandLine_Script_Argument_Container
    */
   public function &setArgument(Light_CommandLine_Script_Argument $newArg )
   {
      foreach ( $this->_arguments as &$arg ) {
         if ( $arg->getName() == $newArg->getName() ) {
            $arg = $newArg;
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
   public function parseArguments( $script, $args = null )
   {

      if ( $args == null || count($args) == 0 ) {
         $args = $_SERVER['argv'];
      }
      
      // fetch the value for the arguments for our scripts out of the arg array.
      $hadErrors = false;
      $errorText = array();
      
      foreach ($this->_arguments as &$arg) {
         $isStr = '--' . $arg->getName();
         $isNotStr = '--no-' . $arg->getName();
         $regex = '/^--' . $arg->getName() . '="?(.+)?"?$/';

         foreach ($args as $tArg) {
            $matches = null; 
            try {
               if ( $isStr == $tArg ) {
                  $arg->setValue(true); 
               } else if ( $isNotStr == $tArg ) {
                  $arg->setValue(false);
               } else if (preg_match($regex, $tArg, $matches)) {
                  if ( isset( $matches[1] ) ) {
                     $tArgValues = explode(',', $matches[1]);
                     foreach ( $tArgValues as $tArgValue ) {
                        $arg->setValue($tArgValue);
                     }
                  } else {
                     $arg->setValue(null);
                  }
               }
            } catch (Exception $ex)  {
               $errorText[] = 'Improperly formatted argument for `' . $arg->getName() . '`.' . "\n";
               $hadErrors = true;
            }
         }

         if ( ! $arg->hasValue() && $arg->hasDefaultValue() ) {
            $arg->setValue($arg->getDefaultValue());
         }

         if ( $arg->getIsRequired() && ! $arg->hasValue() ) {
            $errorText[] = '--' . $arg->getName() . ' is required';
            $hadErrors = true;
         }

      }

      if ( $hadErrors == true ) {
         $this->usage($script, $errorText);
      }

   }

   /**
    * Cleans up regexp patterns for display in the usage()
    * 
    * @param mixed $patt 
    * @access private
    * @return void
    */
   private function prettyPrintPattern( $patt )
   {
      $patt = preg_replace('/^\//', '', $patt);
      $patt = preg_replace('/^\^/', '', $patt);
      $patt = preg_replace('/\/$/', '', $patt);
      $patt = preg_replace('/\$$/', '', $patt);
      return $patt;
   }

   /**
    * Prints out parameter usage for a given argument stack
    *
    * @param array $errorMessage
    */
   public function usage(Light_CommandLine_Script $script, $errorMessage = array() )
   {

      if ($errorMessage != '') {
         $script->message('Error: ');
         foreach ( $errorMessage as $errorLine ) {
            $script->message($errorLine);
         }
      }

      $script->message('');
      $script->message('Usage: ');
      
      $cmdLine = $script->getScriptName();
      
      foreach ($this->_arguments as $arg) { 
         $cmdLine .= ' '; 
         
         if ($arg->getIsRequired() != true) {
            $cmdLine .= '[';
         } 
        
         $cmdLine .= '--' . $arg->getName() . '=' . $this->prettyPrintPattern($arg->getPattern());
         
         if ($arg->getIsRequired() != true) {
            $cmdLine .= ']';
         } 
         
         if ($arg->getDefaultValue() !== NULL) {
            $cmdLine .= '(' . $arg->getDefaultValue() . ')';
         } 
      
      } 
      
      $script->message($cmdLine); 
      $script->message('');
      $script->message('Parameters: ');
      
      foreach ( $this->_arguments as $arg ) {
         $script->message('   --' . $arg->getName() . '=' . $this->prettyPrintPattern($arg->getPattern()));
         $script->message('     Description: ' . $arg->getDescription());
      }

      $script->message('');

      if ( count($errorMessage) > 0 ) {
         $script->done(255);
      } else {
         $script->done(0);
      }

   }

}
