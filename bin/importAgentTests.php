#!/usr/bin/php -q
<?php

require_once( '../bootstrap.php' );
require_once( 'Light/Config.php' );

require_once( 'CTM/User/Cache.php' );

require_once( 'CTM/Test/Folder/Cache.php' );
require_once( 'CTM/Test/Folder/Selector.php' );

require_once( 'CTM/Test/Suite/Cache.php' );

try {
   // Init the cache objects we need.
   $user_cache_obj = new CTM_User_Cache();
   $folder_cache_obj = new CTM_Test_Folder_Cache();
   $suite_cache_obj = new CTM_Test_Suite_Cache();

   $admin_obj = $user_cache_obj->getById( 1 );

   if ( isset( $admin_obj ) ) {

      // admin account exists
      print_r( $admin_obj );

      // see if the regression folder is available.
      $regression_folder = $folder_cache_obj->getByName( 'CTM - Regression' );

      if ( ! isset( $regression_folder ) ) {
         $regression_folder = new CTM_Test_Folder();
         $regression_folder->parent_id = 1;
         $regression_folder->name = 'CTM - Regression';
         $regression_folder->save();
      }

      print_r( $regression_folder );

      // pull open the top level regression suite.
      $regression_suite = $suite_cache_obj->getByName( 'CTM - Regression Suite' );

      if ( ! isset( $regression_suite ) ) {
         $regression_suite = new CTM_Test_Suite();
         $regression_suite->test_folder_id = $regression_folder->id;
         $regression_suite->name = 'CTM - Regression Suite';
         $regression_suite->created_at = time();
         $regression_suite->created_by = $admin_obj->id;
         $regression_suite->modified_at = time();
         $regression_suite->modified_by = $admin_obj->id;
         $regression_suite->test_status_id = 1;
         $regression_suite->save();
      }

      print_r( $regression_suite );

      // TODO: Remove the current plan of the test suite.

      // scan the top level dir for suites to toss into the regression.
      $ctm_base_dir = Light_Config::get('CTM_Config', 'base_dir' );

      $regression_dir = $ctm_base_dir . '/web/regression';

      $fds = scandir( $regression_dir );

      foreach ( $fds as $tf ) {
         $f = $regression_dir . '/' . $tf;

         if ( is_dir( $f ) && $tf != '.' && $tf != '..' ) {
            echo "d: $f\n";

            // Look up the suite folder entry.
            $folder_sel = new CTM_Test_Folder_Selector();
            $folder_and_params = array(
                  new Light_Database_Selector_Criteria( 'parent_id', '=', $regression_folder->id ),
                  new Light_Database_Selector_Criteria( 'name', '=', $tf )
            );

            $folder_objs = $folder_sel->find( $folder_and_params );

            $folder_obj = null;
            if ( count($folder_objs) == 1 ) {
               $folder_obj = $folder_objs[0];
            } else {
               $folder_obj = new CTM_Test_Folder();
               $folder_obj->parent_id = $regression_folder->id;
               $folder_obj->name = $tf;
               $folder_obj->save();
            }

            print_r( $folder_obj );

            // read all the tests from the directory and shove them into the folder.
            // TODO: Read the folder and inject the html into the db.

            // TODO: Add the test into the suite
         }

      }

   }
} catch ( Exception $e ) {
}

echo "Done!";
