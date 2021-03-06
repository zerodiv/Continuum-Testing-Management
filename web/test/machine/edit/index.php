<?php

require_once( '../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Machine.php' );
require_once( 'CTM/Test/Machine/Selector.php' );
require_once( 'CTM/Test/Machine/Browser.php' );
require_once( 'CTM/Test/Machine/Browser/Selector.php' );

class CTM_Site_Test_Machine_Edit extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Test Machine');
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      $id = $this->getOrPost( 'id', '' );
      $isDisabled = $this->getOrPost( 'isDisabled', '' );

      try {
         $sel = new CTM_Test_Machine_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            // print_r( $rows );

            $machine = $rows[0];
            $machine->isDisabled = (int) $isDisabled;
            $machine->lastModified = time();
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

         $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/test/machine/edit/">' );
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
         $this->printHtml( '<td class="odd">' . $this->formatDate( $machine->createdAt ) . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Last Updated:</td>' );
         $this->printHtml( '<td class="odd">' . $this->formatDate( $machine->lastModified ) . '</td>' );
         $this->printHtml( '</tr>' ); 
         
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd">Is Disabled:</td>' );
         $this->printHtml( '<td class="odd">' );
         $this->printHtml( '<select name="isDisabled">' );
         if ( $machine->isDisabled == true ) {
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

      $machine_browsers = null;
      try {
         $sel = new CTM_Test_Machine_Browser_Selector();
         $and_params = array( 
               new Light_Database_Selector_Criteria( 'testMachineId', '=', $id ),
               new Light_Database_Selector_Criteria( 'isAvailable', '=', 1 ),
         );
         $machine_browsers = $sel->find( $and_params );
      } catch ( Exception $e ) {
      }

      $this->printHtml( '<td valign="top">' );
      $this->printHtml( '<table class=ctmTable>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="5">Available Browsers</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>id</th>' );
      $this->printHtml( '<th>name</th>' );
      $this->printHtml( '<th>major version</th>' );
      $this->printHtml( '<th>minor version</th>' );
      $this->printHtml( '<th>patch version</th>' );
      $this->printHtml( '</tr>' );
      if ( count( $machine_browsers ) > 0 ) {
         $browser_cache = Light_Database_Object_Cache_Factory::factory( 'CTM_Test_Browser_Cache' );
         foreach ( $machine_browsers as $machine_browser ) {
            $test_browser = $browser_cache->getById( $machine_browser->testBrowserId );
            $class = $this->oddEvenClass();
            $this->printHtml( '<tr>' );
            $this->printHtml( '<td class="' . $class . '">' . $test_browser->id . '</td>' );
            $this->printHtml( '<td class="' . $class . '">' . $test_browser->name . '</td>' );
            $this->printHtml( '<td class="' . $class . '">' . $test_browser->majorVersion . '</td>' );
            $this->printHtml( '<td class="' . $class . '">' . $test_browser->minorVersion . '</td>' );
            $this->printHtml( '<td class="' . $class . '">' . $test_browser->patchVersion . '</td>' );
            $this->printHtml( '</tr>' );
         }
      } else {
         $this->printHtml( '<tr>' );
         $this->printHtml( '<td class="odd" colspan="5">- No browsers configured for this machine -</td>' );
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
