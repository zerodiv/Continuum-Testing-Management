<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/Suite/Description.php' );
require_once( 'CTM/Test/Suite/Revision.php' );

require_once( 'CTM/Revision/Framework.php' );

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
      $this->addOneToOneRelationship( 'Description', 'CTM_Test_Suite_Description', 'id', 'test_suite_id' );
      $this->addOneToManyRelationship( 'Plan', 'CTM_Test_Suite_Plan', 'id', 'test_suite_id' );
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

   
   public function saveRevision() {

      // save the revision to the revision store.
      $ctm_revision_obj = new CTM_Revision_Framework( 'suite' );
      list( $rv, $revision_id ) = $ctm_revision_obj->addRevision( (integer) $this->id, $this->toXML() );

      if ( $rv == true ) {
         // update the revision database tracker.
         $rev_obj = new CTM_Test_Suite_Revision();
         $rev_obj->test_suite_id = $this->id;
         $rev_obj->modified_at = $this->modified_at;
         $rev_obj->modified_by = $this->modified_by;
         $rev_obj->revision_id = $revision_id;
         $rev_obj->save();
      }

   }

}
