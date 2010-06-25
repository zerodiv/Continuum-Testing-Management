<?php

require_once( '../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Param/Library.php' );
require_once( 'CTM/Test/Param/Library/Selector.php' );

class CTM_Site_Test_Param_Library extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Param Library';
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      return true;
   }

   public function displayBody() {

      $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );

      $params = null;
      try {
         $sel = new CTM_Test_Param_Library_Selector();
         $and_params = array();
         $or_params = array();
         $field_order = array( 'name' );
         $params = $sel->find( $and_params, $or_params, $field_order );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="8">Test Parameters</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiTableTitle">' );
      $this->printHtml( '<td class="aiColumnOne">ID</td>' );
      $this->printHtml( '<td>Name</td>' );
      $this->printHtml( '<td>Created at:</td>' );
      $this->printHtml( '<td>Created by:</td>' );
      $this->printHtml( '<td>Modified at:</td>' );
      $this->printHtml( '<td>Modified by:</td>' );
      $this->printHtml( '<td>Action</td>' );
      $this->printHtml( '</tr>' );

      if ( count( $params ) == 0 ) {
         $class = $this->oddEvenClass();
         $this->printHtml( '<tr class="' . $class . '">' );
         $this->printHtml( '<td colspan="8"><center><b>- There are no parameters defined -</b></td>' );
         $this->printHtml( '</tr>' );
      } else {
         foreach ( $params as $param ) {
            $class = $this->oddEvenClass();
            $created_by = $user_cache->getById( $param->created_by );
            $modified_by = $user_cache->getById( $param->modified_by );

            $this->printHtml( '<tr class="' . $class . '">' );
            $this->printHtml( '<td class="aiColumnOne">' . $param->id . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $param->name ) . '</td>' );
            $this->printHtml( '<td>' . $this->formatDate( $param->created_at ) . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $created_by->username ) . '</td>' );
            $this->printHtml( '<td>' . $this->formatDate( $param->modified_at ) . '</td>' );
            $this->printHtml( '<td>' . $this->escapeVariable( $modified_by->username ) . '</td>' );
            $this->printHtml( '<td><a href="' . $this->_baseurl . '/test/param/library/edit/?id=' . $param->id . '" class="ctmButton">Edit</a></td>' );
            $this->printHtml( '</tr>' );
         }
      }

      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_param_obj = new CTM_Site_Test_Param_Library();
$test_param_obj->displayPage();
