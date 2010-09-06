<?php

require_once('Light/CommandLine/Script/Config.php' );
require_once('Light/CommandLine/Script/Argument/Container.php');

abstract class Light_CommandLine_Script {
   private $_scriptName; 
   private $_arguments;

   function __construct(array $argumentOverrides = array(), $callExecute = true ) {
      $this->_scriptName = null;

      // setup the default timezone.
      date_default_timezone_set( Light_CommandLine_Script_Config::DEFAULT_TIMEZONE() );

      $this->_arguments = new Light_CommandLine_Script_Argument_Container();

      // pull the script name via the argv array.
      if (! empty($_SERVER['argv'][0]))  {
         $this->_scriptName = basename($_SERVER['argv'][0]);
      } else {
         $this->_scriptName = get_class( $this );
      }

      if ( $callExecute == true ) {
         $this->execute( $argumentOverrides ); 
      }

    }

    /**
     * Begins execution of this script: it runs the init, run and done functions.
     * 
     * @access public
     * @return void
     */
    public function execute( $argumentOverrides = array() ) {
       try {
          $this->init();
          
          // parse out all the arguments, and handle the argument overrides if neeeded
          $this->_arguments->parseArguments( $this, $argumentOverrides );

          $this->run();
          $this->done(0);
       } catch ( Exception $e ) {
          $this->message( 'Error caught:' . print_r( $e, true ) );
          $this->done( 255 );
       }
    }

    /**
     * Returns the scripts pretty name
     * 
     * @access public
     * @return string
     */
    public function getScriptName() {
       return $this->_scriptName;
    }

    /**
     * Fluent accessor function for the Light_CommandLine_Script_Argument_Container object.
     * 
     * @access public
     * @return Light_CommandLine_Script_Argument_Container
     */
    public function &arguments() {
       return $this->_arguments;
    }

    /**
     * initalize the script by parsing the arguments for the runtime.
     * 
     * @access public
     * @return void
     */
    public function init() {
       // jeo - DO NOT ADD THINGS HERE: You should be overriding them in your implementation. 
    }

    /**
     * Performs this script's main operation.
     * 
     */
    abstract public function run();

    /**
     * This is called at the completion of a script.
     * 
     * @param int $return_value 
     * @param string $message 
     * @param boolean $do_exit 
     * @access public
     * @return void
     */
    public function done( $return_value = 0, $message = null, $do_exit = true ) {
       if (!empty($errorMessage))  {
          $this->error($errorMessage);
       }
       $this->message( 'Done - returnValue: ' . $return_value );
       if ( $do_exit == true ) {
          exit( $return_value );
       }
       return;
    }

    /**
     * Sends a message to output. Shows a timestamp and context label for new message streams.
     *
     * @param integer $message The message to output.
     * @param boolean $streamState The stream state constant determines whether to show a label and newline or not.
     */
    public function message($message) {
       // TODO: jeo - we need to use stdout for these.
       echo $this->formatDate( time() ) . ' - ' . $message . "\n";
    }


    /**
     * Sends an error to the appropriate output. Shows a timestamp and context label for new message streams.
     *
     * @param integer $message The message to output.
     * @param boolean $streamState The stream state constant determines whether to show a label and newline or not.
     */
    public function error($message) {
       // TODO: jeo - we need to use stderr for these.
       echo $this->formatDate( time() ) . ' - ' . $message . "\n";
    }

    public function formatDate( $timestamp ) {
       return date( Light_CommandLine_Script_Config::TIME_FORMAT(), $timestamp );
    }

}
