<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/Suite/Description.php' );
require_once( 'CTM/Test/Suite/Description/Selector.php' );

class CTM_Test_Suite extends Light_Database_Object {
   public $id;
   public $test_folder_id;
   public $name;
   public $created_at;
   public $created_by;
   public $modified_at;
   public $modified_by;
   public $test_status_id;

   public function init() {
      $this->setSqlTable( 'test_suite' );
      $this->setDbName( 'suite' );
   }

   // overloaded remove to take care of the object cleanup
   public function remove() {
      try {
         
         $desc_obj = $this->getDescription(); 
         
         if ( isset( $desc_obj ) ) {
            $desc_obj->remove();
         } 
         
         // now remove ourselves
         parent::remove(); 
      } catch ( Exception $e ) {
         throw $e;
      } 
   }

   public function setDescription( $description ) {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $a_obj = $this->getDescription();
         if ( isset( $a_obj ) ) {
            $a_obj->description = $description;
            $a_obj->save();
         } else {
            $a_obj = null;
            $a_obj = new CTM_Test_Suite_Description();
            $a_obj->test_suite_id = $this->id;
            $a_obj->description = $description;
            $a_obj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getDescription() {
      if ( ! isset( $this->id ) ) {
         return null; 
      } 
      try {
         $sel = new CTM_Test_Suite_Description_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_suite_id', '=', $this->id ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }

}
