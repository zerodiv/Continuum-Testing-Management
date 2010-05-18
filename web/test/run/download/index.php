<?php

require_once( '../../../../bootstrap.php' );
require_once( 'Light/Config.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test/Run/Selector.php' );

class CTM_Site_Test_Run_Download extends CTM_Site { 

   public function setupPage() {
      return true;
   }

   public function handleRequest() {
      $id = $this->getOrPost( 'id', '' );

      try {

         if ( $id > 0 ) {

            $test_run_sel = new CTM_Test_Run_Selector();
            $and_params = array( new Light_Database_Selector_Criteria( 'id', '=', $id ) );
            $test_runs = $test_run_sel->find( $and_params );

            if ( isset( $test_runs[0] ) ) {
               $test_run = $test_runs[0];
               // test run found, check for a zip file.
               $zip_file = Light_Config::get('CTM_Config', 'SUITE_DIR') . '/' . $test_run->id . '.zip';

               if ( is_file($zip_file) ) {

                  $fh = fopen( $zip_file, 'r' );

                  if ( is_resource( $fh ) ) {
                     header( 'Content-type: application/zip' );
                     header( 'Content-disposition: attatchment; filename="test_run_' . $id . '.zip"' );
                     fpassthru( $fh );
                     fclose( $fh );
                     return false;
                  }

               } else {
                  // no zip file?
                  echo 'no zip file for test run.';
                  return false;
               }

            } else {
               echo 'invalid id';
               return false;
            }

         } else {
            echo 'no id provided';
            return false;
         }


      } catch ( Exception $e ) {
         echo 'unable to find test run by id provided';
         return false;
      }

      // Even if we didn't send a file we do not need to go further.
      echo 'Invalid file.';
      return false;

   }
                           

   public function displayBody() {
      return true;
   }

}

$test_add_obj = new CTM_Site_Test_Run_Download();
$test_add_obj->displayPage();
