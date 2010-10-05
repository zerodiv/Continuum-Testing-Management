<?php

require_once( 'Light/Config.php' );

class CTM_Revision_Framework
{
   private $_gitDir;
   private $_gitCommand;
   private $_basedir;
   private $_namespace;

   function __construct( $namespace )
   {

      $acceptableNamespaces = array( 'test', 'suite' );
      if ( ! in_array($namespace, $acceptableNamespaces) ) {
         throw new Exception(
               'Acceptable namepsaces are: ' .
               join(', ', $acceptableNamespaces) .
               ' you provided: ' . $namespace
         );
      }

      $this->_namespace = $namespace;

      $this->_gitDir = Light_Config::get('CTM_Config', 'git_dir');
      $this->_gitCommand = Light_Config::get('CTM_Config', 'git_command');
      
      if ( ! is_dir($this->_gitDir) ) {
         throw new Exception('Failed to find git_dir: ' . $this->_gitDir);
      }

      if ( ! is_executable($this->_gitCommand) ) {
         throw new Exception('Failed to find git_command: ' . $this->_gitCommand);
      }

      $this->_basedir = $this->_gitDir . '/' . $namespace;

      if ( ! is_dir($this->_basedir) ) {
         throw new Exception( 'basedir is invalid: ' . $this->_basedir );
      }

   }

   public function checkId( $id )
   {
      if ( ! is_int($id) || $id < 0 ) {
         throw new Exception( 'id is required to be a positive integer value: ' . $id . ' was provided' );
      }
   }

   public function getHashId( $id )
   {
      try {
         $this->checkId($id);
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

   public function getFilenameFromId( $id )
   {
      $this->checkId($id);

      // hash up a path ../test/12/34/56
      $fname = $this->_basedir . '/' . $this->getHashId($id);

      // try to create the target directory
      if ( ! is_dir($fname) ) {

         mkdir($fname, 0777, true);

         if ( ! is_dir($fname) ) {
            throw new Exception( 'Failed to make target directory: ' . $fname );
         }

      }

      // slap the filename on: ../test/12/34/56/123456.test
      $fname .= '/' . $id . '.' . $this->_namespace;

      return $fname;

   }

   public function createShortNameFromFileName( $filename )
   {
      return './' . str_replace($this->_gitDir . '/', '', $filename);
   }

   public function addRevision( $id, $data )
   {

      try {

         $this->checkId($id);

         $fname = $this->getFileNameFromId($id);

         $fh = fopen($fname, 'w');

         if ( ! is_resource($fh) ) {
            throw new Exception( 'Failed to open write handle to: ' . $fname );
         }

         fwrite($fh, $data);

         fclose($fh);

         $shortname = $this->createShortNameFromFileName($fname);

         // okay the file is written now we need to commit it into the repo.
         $addCmd = 
            'cd ' . $this->_gitDir . ' ; ' . 
            $this->_gitCommand . ' ' . 
            ' add ' . 
            $shortname;

         exec($addCmd, $addOutput, $addRv);

         if ( $addRv != 0 ) {
            throw new Exception( 'Failed to add revision' );
         }

         $commitCmd = 
            'cd ' . $this->_gitDir . ' ; ' .
            $this->_gitCommand . ' ' .
            ' commit ' . 
            ' -m "' . $this->_namespace . ' - ' . $id . ' - changed" ' .
            $shortname
         ;

         exec($commitCmd, $commitOutput, $commitRv);

         if ( $commitRv != 0 ) {
            throw new Exception( 'Failed to commit revision' );
         }

         // [0] => [master 4992f22] 24 - changed
         if ( preg_match('/\[master (.*?)\]/', $commitOutput[0], $pregs) ) {
            $revisionId = $pregs[1];
            return array( true, $revisionId );
         }

         return array( false );

      } catch ( Exception $e ) {
         throw $e;
      }

   }

   public function diffRevision( $id, $cur, $prev )
   {
      try {

         $this->checkId($id);

         $fname = $this->getFileNameFromId($id);

         $shortname = $this->createShortNameFromFileName($fname);

         // /opt/local/bin/git diff 5d7ca17..644489d ./test/2/2.test
         $diffCmd = 
            'cd ' . $this->_gitDir . ' ; ' . 
            $this->_gitCommand . ' ' .
            ' diff ' .
            ' -U25000 ' . // unify the diff so we don't have to do anything fancy ;P
            $cur . '..' . $prev . ' ' . $shortname;

         // echo "diffCmd: $diffCmd<br>\n";

         $pipeSpec = array( 
               0 => array('pipe', 'r'),
               1 => array('pipe', 'w'),
               2 => array('pipe', 'w')
         );

         $pipes = array();

         $diffCmdH = proc_open($diffCmd, $pipeSpec, $pipes);

         if ( is_resource($diffCmdH) ) {
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            return array(true, $stdout, $stderr);
         }

         return array( false );

      } catch ( Exception $e ) {
         throw $e;
      }
   }

}
