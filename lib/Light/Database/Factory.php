<?php

require_once( 'Light/Database/Factory/Config.php' );

// Factory class that is the static container for the impl that handles the actual
// Heavy 'work' for creating connections and handleing pooling.
class Light_Database_Factory {
   static private $_db_factory;

   static public function getDBH( $name, $pooled = true ) {
      if ( ! isset( Light_Database_Factory::$_db_factory ) ) {
         Light_Database_Factory::$_db_factory = new Light_Database_Factory_Impl();
      }
      return Light_Database_Factory::$_db_factory->getDBH( $name, $pooled );
   }

}

class Light_Database_Factory_Impl {
   private $_config;
   private $_connection_pool;

   function __construct() {
      // load our database configs.
      $this->_config = parse_ini_file( Light_Database_Factory_Config::CONFIG_FILE(), true );
   } 

   public function getDBH( $name, $pooled = true ) {
      // check to see if this is a valid name for connections
      if ( ! isset( $this->_config[ $name ] ) ) {
         throw new Exception( 'Failed to find database connection: ' . $name );
      }
      // if we have a pooled connection pull it out of the assoc hash
      if ( $pooled == true && isset( $this->_connection_pool[ $name ] ) ) {
         return $this->_connection_pool[ $name ];
      }

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
         $this->_connection_pool[ $name ] = $dbh;
      }
      return $dbh;
   }

}
