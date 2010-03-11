<?php

class PFL_SVN_Storage {
   private $_svn_command;
   private $_svn_root;
   private $_svn_checkout;

   function __construct() {
      $this->_svn_command  = PFL_SVN_Storage_Config::SVN_COMMAND();
      $this->_svn_root     = PFL_SVN_Storage_Config::SVN_ROOT();
      $this->_svn_checkout = PFL_SVN_Storage_Config::SVN_CHECKOUT();
   }

   public function getFile() {
      // get a file from the version control
   }

   public function storeFile() {
      // stores a file to the version control
   }

}
