<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Html/Source.php' );
require_once( 'CTM/Test/Html/Source/Selector.php' );

class CTM_Site_Test_Download extends CTM_Site { 

   public function setupPage() {
      $this->_pagetitle = 'Test Download';
      return true;
   }

   public function handleRequest() {

      $this->requiresAuth();

      $id               = $this->getOrPost( 'id', '' );

      if ( $id == '' ) {
         return true;
      }

      header( 'Content-type: text/html' );
      header( 'Content-disposition: attatchment; filename="test_' . $id . '.html"' );

      try {
         $sel = new CTM_Test_Html_Source_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'testId', '=', $id ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            $test_html = $rows[0];
            echo $test_html->html_source;
            exit();
         }
      } catch ( Exception $e ) {
      }

      return true;

   }
                           

   public function displayBody() {
      return true;
   }

}

$test_edit_obj = new CTM_Site_Test_Download();
$test_edit_obj->displayPage();
