<?php

require_once( 'Light/Config.php' );

class CTM_Revision_Framework {
   private $_git_dir;
   private $_git_command;
   private $_basedir;
   private $_namespace;

   function __construct( $namespace ) {

      $acceptable_namespaces = array( 'test', 'suite' );
      if ( ! in_array( $namespace, $acceptable_namespaces ) ) {
         throw new Exception( 'Acceptable namepsaces are: ' . join( ', ', $acceptable_namespaces ) . ' you provided: ' . $namespace );
      }

      $this->_namespace = $namespace;

      $this->_git_dir = Light_Config::get( 'CTM_Config', 'git_dir' );
      $this->_git_command = Light_Config::get( 'CTM_Config', 'git_command' );
      
      if ( ! is_dir( $this->_git_dir ) ) {
         throw new Exception( 'Failed to find git_dir: ' . $this->_git_dir );
      }

      if ( ! is_executable( $this->_git_command ) ) {
         throw new Exception( 'Failed to find git_command: ' . $this->_git_command );
      }

      $this->_basedir = $this->_git_dir . '/' . $namespace;

      if ( ! is_dir( $this->_basedir ) ) {
         throw new Exception( 'basedir is invalid: ' . $this->_basedir );
      }

   }

   public function checkId( $id ) {
      if ( ! is_int( $id ) || $id < 0 ) {
         throw new Exception( 'id is required to be a positive integer value: ' . $id . ' was provided' );
      }
   }

   public function getHashId( $id ) {

      try {
         $this->checkId( $id );
      } catch ( Exception $e ) {
         throw $e;
      }

      $rawId = $id;
      $modPath = array();
      while (strlen($rawId) > 1) {
         $modPath[] = substr($rawId, 0, 2);
         $rawId = substr($rawId, 2);
      }
      if (strlen($rawId) > 0) {
         $modPath[] = $rawId;
      }
      return implode('/', $modPath);
   }

   public function getFilenameFromId( $id ) {
      $this->checkId( $id );

      // hash up a path ../test/12/34/56
      $fname = $this->_basedir . '/' . $this->getHashId( $id );

      // try to create the target directory
      if ( ! is_dir( $fname ) ) {

         mkdir( $fname, 0777, true );

         if ( ! is_dir( $fname ) ) {
            throw new Exception( 'Failed to make target directory: ' . $fname );
         }

      }

      // slap the filename on: ../test/12/34/56/123456.test
      $fname .= '/' . $id . '.' . $this->_namespace;

      return $fname;

   }

   public function addRevision( $id, $data ) {

      try {

         $this->checkId( $id );

         $fname = $this->getFileNameFromId( $id );

         $fh = fopen( $fname, 'w' );

         if ( ! is_resource( $fh ) ) {
            throw new Exception( 'Failed to open write handle to: ' . $fname );
         }

         fwrite( $fh, $data );

         fclose( $fh );

         $shortname = './' . str_replace( $this->_git_dir . '/', '', $fname );

         // okay the file is written now we need to commit it into the repo.
         $add_cmd = 
            'cd ' . $this->_git_dir . ' ; ' . 
            $this->_git_command . ' ' . 
            ' add ' . 
            $shortname;

         exec( $add_cmd, $add_output, $add_rv );

         if ( $add_rv != 0 ) {
            throw new Exception( 'Failed to add revision' );
         }

         $commit_cmd = 
            'cd ' . $this->_git_dir . ' ; ' .
            $this->_git_command . ' ' .
            ' commit ' . 
            ' -m "' . $this->_namespace . ' - ' . $id . ' - changed" ' .
            $shortname
         ;

         exec( $commit_cmd, $commit_output, $commit_rv );

         if ( $commit_rv != 0 ) {
            throw new Exception( 'Failed to commit revision' );
         }

         // [0] => [master 4992f22] 24 - changed
         if ( preg_match( '/\[master (.*?)\]/', $commit_output[0], $pregs ) ) {
            $revision_id = $pregs[1];
            return array( true, $revision_id );
         }

         return array( false );

      } catch ( Exception $e ) {
         throw $e;
      }

   }

}
