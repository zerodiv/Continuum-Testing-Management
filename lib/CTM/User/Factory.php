<?php

require_once( 'CTM/User.php' );
require_once( 'CTM/User/Factory/Config.php' );

class CTM_User_Factory {
   public $_dbh;

   function __construct() {
   }

   public function getDBH() {
      if ( isset( $this->_dbh ) ) {
         return $this->_dbh;
      }
      $this->_dbh = null;
      try {
         $this->_dbh = new PDO( CTM_User_Factory_Config::DB_DSN(), CTM_User_Factory_Config::DB_USERNAME(), CTM_User_Factory_Config::DB_PASSWORD() );
      } catch ( Exception $e ) {
         return;
      }
      return $this->_dbh;
   }

   public function cleanupUserName( $username ) {
      $username = strtolower( $username );
      $username = ltrim( $username );
      $username = rtrim( $username );
      return $username;
   }

   public function createUser( $username, $password ) {

      try {

         // preserve the case sensitivity of email addresses
         $email_address = $username;

         $username = $this->cleanupUserName( $username );

         $dbh = $this->getDBH();

         if ( ! is_object( $dbh ) ) {
            return array( false, 'Unable to connect to the user database at this time.' );
         }

         // try to see if there is a matching username.
         $verify_sth = $dbh->prepare( 'SELECT * FROM account WHERE username = ?' );
         $verify_sth->bindParam( 1, $username );
         $verify_sth->execute();

         $verify_user = $verify_sth->fetch( PDO::FETCH_ASSOC );

         if ( isset( $verify_user['id'] ) ) {
            return array( false, 'There is already a user by that username in the system.' );
         }

         $verify_sth = null;

         // we do not have a matching user go ahead and add them in.
         $created_on = time();
         $ins_sth = $dbh->prepare( 'INSERT INTO account ( username, password, email_address, created_on ) VALUES ( ?, ?, ?, ? )' );
         $ins_sth->bindParam( 1, $username );
         $ins_sth->bindParam( 2, md5( $password ) );
         $ins_sth->bindParam( 3, $email_address );
         $ins_sth->bindParam( 4, $created_on );
         $ins_sth->execute();

         // assuming we created succesfully we should be able to pull them back now.
         $verify_sth = $dbh->prepare( 'SELECT * FROM account WHERE username = ? AND password = ?' );
         $verify_sth->bindParam( 1, $username );
         $verify_sth->bindParam( 2, md5( $password ) );
         $verify_sth->execute();

         $verify_user = $verify_sth->fetch( PDO::FETCH_ASSOC );

         if ( isset( $verify_user['id'] ) && $verify_user['id'] > 0 ) {
            $user = new CTM_User();
            $user->consumeHash( $verify_user );
            return array( true, $user );
         }

         return array( false, 'Failed to create user.' );

      } catch ( Exception $e ) {
         return array( false, 'Unable to process createUser at this time.' );
      }

      return array( false, 'Unable to connect to the user database at this time.' );

   }

   public function loginUser( $username, $password ) {
      try {
         
         $username = $this->cleanupUserName( $username );

         $dbh = $this->getDBH();

         if ( ! is_object( $dbh ) ) {
            return array( false, 'Failed to verify user at this time.' );
         }

         $login_sth = $dbh->prepare( 'SELECT * FROM account WHERE username = ? AND password = ?' );
         $login_sth->bindParam( 1, $username );
         $login_sth->bindParam( 2, md5( $password ) );
         $login_sth->execute();

         $login_user = $login_sth->fetch( PDO::FETCH_ASSOC );

         if ( isset( $login_user['id'] ) && $login_user['id'] > 0 ) {
            $user = new CTM_User();
            $user->consumeHash( $login_user );
            return array( true, $user );
         }

         return array( false, 'Invalid username or password.' );

      } catch ( Exception $e ) {
         return array( false, 'Failed to connect to user database at this time.' );
      }
      return array( false, 'Failed to connect to user database at this time.' );
   }

   public function lookupByUsername( $username ) {

      try {
         $username = $this->cleanupUserName( $username );

         $dbh = $this->getDBH();

         if ( ! is_object( $dbh ) ) {
            return array( false, 'Failed to find user account.' );
         }

         $user_sth = $dbh->prepare( 'SELECT * FROM account WHERE username = ?' );
         $user_sth->bindParam( 1, $username );
         $user_sth->execute();

         $t_user = $user_sth->fetch( PDO::FETCH_ASSOC );

         if ( isset( $t_user['id'] ) && $t_user['id'] > 0 ) {
            $user = new CTM_User();
            $user->consumeHash( $t_user );
            return array( true, $user );
         }

         return array( false, 'Failed to find user in database.' );

      } catch ( Exception $e ) {
         return array( false, 'Database error: ' . print_r( $e, true ) );
      }

      return array( false, 'Failed to lookup user.' );

   }

}
