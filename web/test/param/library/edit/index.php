<?php

require_once( '../../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Param/Library.php' );
require_once( 'CTM/Test/Param/Library/Selector.php' );

class CTM_Site_Test_Param_Library extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Param Library - Edit';
      return true;
   }

   public function handleRequest() {
      $action = $this->getOrPost( 'action', '' );
      $id = $this->getOrPost( 'id', '' );
      $default_value = $this->getOrPost( 'default_value', '' );
      $description = $this->getOrPost( 'description', '' );
     
      $this->requiresAuth();

      if ( $action != 'save' ) {
         return true;
      }

      try {
         $param = null;
         $params = null;

         $sel = new CTM_Test_Param_Library_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );
         $params = $sel->find( $and_params );

         if ( isset( $params[0] ) ) {
            $param = $params[0];

            $param->setDescription( $description );
            $param->setDefault( $default_value );

         }

      } catch ( Exception $e ) {
      }

      return true;
   }

   public function displayBody() {

      $id = $this->getOrPost( 'id', '' );

      $user_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );

      $param = null;
      try {
         $sel = new CTM_Test_Param_Library_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );
         $params = $sel->find( $and_params );

         if ( isset( $params[0] ) ) {
            $param = $params[0];
         }
      } catch ( Exception $e ) {
      }

      $createdBy = $user_cache->getById( $param->createdBy );
      $modifiedBy = $user_cache->getById( $param->modifiedBy );
      $desc_obj = $param->getDescription();
      $def_obj = $param->getDefault();

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      
      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/param/library/edit/">' );
      $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' );
      $this->printHtml( '<input type="hidden" value="save" name="action">' );

      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">Edit Test Parameter</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Name:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( $param->name ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Created at:</td>' );
      $this->printHtml( '<td>' . $this->formatDate( $param->createdAt ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Created by:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( $createdBy->username ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Modified at:</td>' );
      $this->printHtml( '<td>' . $this->formatDate( $param->modifiedAt ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>Modified by:</td>' );
      $this->printHtml( '<td>' . $this->escapeVariable( $modifiedBy->username ) . '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2">Description:</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2"><center><textarea name="description" cols="60" rows="20">' . $this->escapeVariable( $desc_obj->description ) . '</textarea></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2">Default Value:</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td colspan="2"><center><textarea name="default_value" cols="60" rows="20">' . $this->escapeVariable( $def_obj->default_value ) . '</textarea></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="aiButtonRow">' );
      $this->printHtml( '<td colspan="2"><center><input type="submit" value="Save"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );

      $this->printHtml( '</form>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$test_param_obj = new CTM_Site_Test_Param_Library();
$test_param_obj->displayPage();
