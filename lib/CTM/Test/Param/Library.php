<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/Test/Param/Library/Default.php' );
require_once( 'CTM/Test/Param/Library/Default/Selector.php' );
require_once( 'CTM/Test/Param/Library/Description.php' );
require_once( 'CTM/Test/Param/Library/Description/Selector.php' );

class CTM_Test_Param_Library extends Light_Database_Object
{
   public $id;
   public $name;
   public $createdAt;
   public $createdBy;
   public $modifiedAt;
   public $modifiedBy;

   public function init()
   {
      $this->setSqlTable('test_param_library');
      $this->setDbName('test');
   }

   public function setDefault( $default )
   {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $defObj = $this->getDefault();
         if ( isset( $defObj ) ) {
            $defObj->defaultValue = $default;
            $defObj->save();
            return true;
         } else {
            $defObj = new CTM_Test_Param_Library_Default();
            $defObj->testParamLibraryId = $this->id;
            $defObj->defaultValue = $default;
            $defObj->save();
            if ( $defObj->id > 0 ) {
               return true;
            }
            return false;
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getDefault()
   {
      if ( ! isset( $this->id ) ) {
         return null;
      }
      try {
         $sel = new CTM_Test_Param_Library_Default_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testParamLibraryId', '=', $this->id ) );
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

   public function setDescription( $description )
   {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $defObj = $this->getDescription();
         if ( isset( $defObj ) ) {
            $defObj->description = $description;
            $defObj->save();
            return true;
         } else {
            $defObj = new CTM_Test_Param_Library_Description();
            $defObj->testParamLibraryId = $this->id;
            $defObj->description = $description;
            $defObj->save();
            if ( $defObj->id > 0 ) {
               return true;
            }
            return false;
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getDescription()
   {
      if ( ! isset( $this->id ) ) {
         return null;
      }
      try {
         $sel = new CTM_Test_Param_Library_Description_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testParamLibraryId', '=', $this->id ) );
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
