<?php

require_once( 'Light/Database/Object/Cache.php' );

// needed for the getFolderChildren call.. even though we don't cache it at this point.
require_once( 'CTM/Test/Suite/Folder/Selector.php' );

class CTM_Test_Suite_Folder_Cache extends Light_Database_Object_Cache
{

   public function init()
   {
      $this->setObject('CTM_Test_Suite_Folder');
   }

   public function getFolderParents( $parentId, &$parents )
   {
      try {
         $parent = $this->getById($parentId);
         if ( isset($parent) ) {
            $parents[] = $parent;
            if ( $parent->parentId > 0 ) {
               $this->getFolderParents($parent->parentId, $parents);
            }
         }
      } catch( Exception $e ) {
         throw $e;
      }
   }

   public function getFolderChildren( $parentId )
   {
      try {
         $sel = new CTM_Test_Suite_Folder_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'parentId', '=', $parentId ) );
         $rows = $sel->find($andParams);
         return $rows;
      } catch ( Exception $e ) {
         throw $e;
      }
   }

   public function getChildByName( $parentId, $name )
   {
      try {
         $sel = new CTM_Test_Suite_Folder_Selector();
         $andParams = array( 
               new Light_Database_Selector_Criteria( 'parentId', '=', $parentId ),
               new Light_Database_Selector_Criteria( 'name', '=', $name )
         );
         $rows = $sel->find($andParams);
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
