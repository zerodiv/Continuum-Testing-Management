<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Machine.php' );
require_once( 'CTM/Test/Machine/Selector.php' );

class CTM_Site_Test_Machine_Edit extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Machine';
      return true;
   }

   public function handleRequest() {
      $id = $this->getOrPost( 'id', '' );
      $is_disabled = $this->getOrPost( 'is_disabled', '' );

      try {
         $sel = new CTM_Test_Machine_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            // print_r( $rows );

            $machine = $rows[0];
            $machine->is_disabled = (int) $is_disabled;
            $machine->save();

            // print_r( $machine );

         }

      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      $id = $this->getOrPost( 'id', '' );

      $rows = null;
      try {
         $sel = new CTM_Test_Machine_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );

         $rows = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<center>' );

      $this->printHtml( '<table>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<td valign="top">' );

      $this->printHtml( '<table class="ctmTable">' );
      if ( count( $rows ) == 1 ) {
         $machine = $rows[0];

         $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/test/machine/edit/">' );
         $this->printHtml( '<input type="hidden" value="' . $id . '" name="id">' );
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<th colspan="4">Edit Machine</th>' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Hostname:</td>' );
         $this->printHtml( '<td class="odd">' . $machine->hostname . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">OS:</td>' );
         $this->printHtml( '<td class="odd">' . $machine->os . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Created At:</td>' );
         $this->printHtml( '<td class="odd">' . $this->formatDate( $machine->created_at ) . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Last Updated:</td>' );
         $this->printHtml( '<td class="odd">' . $this->formatDate( $machine->last_modified ) . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Is Disabled:</td>' );
         $this->printHtml( '<td class="odd">' );
         $this->printHtml( '<select name="is_disabled">' );
         if ( $machine->is_disabled == true ) {
            $this->printHtml( '<option value="1" selected>Yes</option>' );
            $this->printHtml( '<option value="0">No</option>' );
         } else {
            $this->printHtml( '<option value="1">Yes</option>' );
            $this->printHtml( '<option value="0" selected>No</option>' );
         }
         $this->printHtml( '</select>' );
         $this->printHtml( '</td>' );
         $this->printHtml( '</tr>' ); 

         $this->printHtml( '<tr>' );
         $this->printHtml( '<td colspan="2" class="even"><center><input type="submit" value="Save"></center></td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '</form>' ); 
      } else {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="row">Failed to find: ' . $this->escapeVariable( $id ) . '</td>' );
         $this->printHtml( '</tr>' );
      }

      $this->printHtml( '</table>' );
      $this->printHtml( '</td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</table>' );

      $this->printHtml( '</center>' );

      return true;
   }

}

$test_machine_edit_obj = new CTM_Site_Test_Machine_Edit();
$test_machine_edit_obj->displayPage();
