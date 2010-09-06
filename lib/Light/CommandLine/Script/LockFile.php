<?php

class Light_CommandLine_Script_LockFile {
   /**
    * The base lock directory to work off of defaults to /tmp
    * 
    * @var string
    * @access private
    */
   private $_lockDir;

   /**
    * The constructed filename for the lock in the format of "/tmp/$lockName_" . md5( $lockName ) . ".lock"
    * 
    * @var mixed
    * @access private
    */
   private $_lockFileName;

   function __construct( $lockName, $implicitLock = true ) {
      $this->_lockDir = '/tmp'; 
      // setup the lock
      $this->_lockFileName = $this->createLockFileName( $lockName );
      // if they would like us to establish a lock now then do so
      if ( $implicitLock == true ) {
         try {
            $this->lock();
         } catch ( Exception $e ) {
            throw $e;
         }
      }
   }

   /**
    * Creates a lock file name given a lockName
    * 
    * @param string $lockName 
    * @access public
    * @return string
    */
   public function createLockFileName( $lockName ) {
      return $this->_lockDir . '/light_' . md5( $lockName ) . '.lock';
   }

   /**
    * Gets the current lock file name
    * 
    * @access public
    * @return void
    */
   public function getLockFileName() {
      return $this->_lockFileName;
   }

   /**
    * Establishes a lock via a lock file in the lockDir.
    * 
    * @access public
    * @throws Exception
    * @return void
    */
   public function lock() {
      
      if ( is_file( $this->_lockFileName ) ) {
         if ( ! is_readable( $this->_lockFileName ) ) {
            throw new Exception( 'Unable to read from lock file: ' . $this->_lockFileName );
         }
         if ( ! is_writeable( $this->_lockFileName ) ) {
            throw new Exception( 'Unable to write to lock file: ' . $this->_lockFileName );
         }
         
         // file already exists.. see if the process is still alive.
         $running_pid = file_get_contents( $this->_lockFileName );
         $running_pid = trim( $running_pid );

         $kill_value = posix_kill( $running_pid, 0 );
         $err_value = posix_get_last_error();
         if ( $kill_value === true ) {
            // this is up and running already.
            throw new Exception( 'This process is already runnning with a pid of: ' . $running_pid );
         }

         // we should establish a lock at this point
         try {
            return $this->_establishLock();
         } catch ( Exception $e ) {
            throw $e;
         }

      }

      try {
         return $this->_establishLock();
      } catch ( Exception $e ) {
         throw $e;
      }

   }

   /**
    * Actually does the heavy lifting of creating the lock file and putting the pid there.
    * 
    * @access private
    * @return void
    */
   private function _establishLock() {

      $fh = fopen( $this->_lockFileName, 'w' );

      if ( ! is_resource( $fh ) ) {
         throw new Exception( 'Failed to establish a lock via lock file: ' . $this->_lockFileName );
      }

      fwrite( $fh, posix_getpid() );

      fclose( $fh );

      return true;

   }

   /**
    * Removes a lockfile from disk
    * 
    * @access public
    * @return void
    */
   public function unlock() {
      @unlink( $this->_lockFilename );
   }

   /**
    * By default we try to cleanup from ourselves
    * 
    * @access protected
    * @return void
    */
   function __destruct() {
      $this->unlock();
   }

}
