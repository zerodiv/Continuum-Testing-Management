<?php

class Light_Database_Connection_Object
{
   private $_config;
   private $_connectionPool;

   function __construct()
   {
      // load our database configs.
      $this->_config = parse_ini_file(
          Light_Config::get(
              'Light_Database_Connection_Factory_Config', 
              'CONFIG_FILE'
          ),
          true
      );
      $this->_connectionPool = array();
   } 

   public function getDBH( $name, $pooled = true )
   {
      // check to see if this is a valid name for connections
      if ( ! isset( $this->_config[ $name ] ) ) {
         throw new Exception( 'Failed to find database connection: ' . $name );
      }
      // if we have a pooled connection pull it out of the assoc hash
      if ( $pooled == true && isset( $this->_connectionPool[ $name ] ) ) {
         return $this->_connectionPool[ $name ];
      }

      // print_r( $this->_config[$name] );

      $dbh = null;
      try {
         $dbh = new PDO(
               $this->_config[$name]['db_dsn'],
               $this->_config[$name]['db_username'],
               $this->_config[$name]['db_password']
         );
      } catch ( Exception $e ) {
         return;
      }
      // save the connection to the connection pool
      if ( $pooled == true ) {
         $this->_connectionPool[ $name ] = $dbh;
      }
      return $dbh;
   }

}
