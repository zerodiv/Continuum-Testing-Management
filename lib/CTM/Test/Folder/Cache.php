<?php

require_once( 'Light/Database/Object/Cache.php' );

// needed for the getFolderChildren call.. even though we don't cache it at this point.
require_once( 'CTM/Test/Folder/Selector.php' );

class CTM_Test_Folder_Cache extends Light_Database_Object_Cache {

   public function init() {
      $this->setObject( 'CTM_Test_Folder' );
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

   public function getFolderChildren( $parent_id ) {
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'parent_id', '=', $parent_id ) );
         $rows = $sel->find( $and_params );
         return $rows;
      } catch ( Exception $e ) {
         throw $e;
      }
   }

   public function getChildByName( $parent_id, $name ) {
      try {
         $sel = new CTM_Test_Folder_Selector();
         $and_params = array( 
               new Light_Database_Selector_Criteria( 'parent_id', '=', $parent_id ),
               new Light_Database_Selector_Criteria( 'name', '=', $name )
         );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
         return null;
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }


}
