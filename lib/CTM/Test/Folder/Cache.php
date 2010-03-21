<?php

require_once( 'CTM/Test/Folder.php' );
require_once( 'CTM/Test/Folder/Selector.php' );

class CTM_Test_Folder_Cache {
   private $_cache;
   private $_loaded_all;

   function __construct() {
      $this->_cache = array();
      $this->_loaded_all = false;
   }

   public function getAll() {
      if ( $this->_loaded_all == true ) {
         return $this->_cache;
      }
      try {
         $sel = new CTM_Test_Folder_Selector();
         $rows = $sel->find();

         if ( count( $rows ) > 0 ) {
            $this->_cache = $rows;
            $this->_loaded_all = true;
            return $rows;
         }

         // not found
         return null;

      } catch ( Exception $e ) {
         throw $e;
      }
      // nothing found
      return null;
   }

   public function getFolderParents( $parent_id, &$parents ) {
      try {
         $parent = $this->getById( $parent_id );
         if ( isset( $parent ) ) {
            $parents[] = $parent;
            if ( $parent->parent_id > 0 ) {
               $this->getFolderParents( $parent->parent_id, $parents );
            }
         }
      } catch( Exception $e ) {
         throw $e;
      }
   }

   public function getHierarchy() {
      // this will build a hash with the key being 'Name'/'Child'/'Grand Child'/...
      try {
         $hiearchy = array();

         $folders = $this->getAll();

         // iterate across all the folders
         foreach ( $folders as $folder ) {
            $parents = array();

            // get the parents for the folder
            $this->getFolderParents( $folder->parent_id, $parents );

            // init the hash key
            $key = $folder->name;

            if ( count( $parents ) > 0 ) {

               $parents = array_reverse( $parents );

               foreach ( $parents as $parent ) {
                  $key = $parent->name . '[0]' . $key;
               }
            }

            $hiearchy[ $key ] = $folder;

         }

         return $hiearchy;

      } catch ( Exception $e ) {
         throw $e;
      }

      return array();

   }

   public function getById( $id ) {
      // iterate across the cache
      foreach ( $this->_cache as $cached ) {
         if ( $cached->id == $id ) {
            return $cached;
         }
      }
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array(
               new Light_Database_Selector_Criteria( 'id', '=', $id ),
         );
         $rows = $sel->find( $and_params );

         if ( isset( $rows[0] ) ) {
            $this->_cache[] = $rows[0];
            return $rows[0];
         }

         // not found.
         return null;

      } catch ( Exception $e ) {
         throw $e;
      }

      // not found
      return null;
   }

}
