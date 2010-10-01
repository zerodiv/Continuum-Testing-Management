<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/Suite/Description.php' );
require_once( 'CTM/Test/Suite/Revision.php' );
require_once( 'CTM/Test/Suite/Plan/Selector.php' );

require_once( 'CTM/Revision/Framework.php' );

class CTM_Test_Suite extends Light_Database_Object
{
   public $id;
   public $testFolderId;
   public $name;
   public $created_at;
   public $created_by;
   public $modified_at;
   public $modified_by;
   public $test_status_id;

   public function init()
   {
      $this->setSqlTable('test_suite');
      $this->setDbName('suite');
      $this->addOneToOneRelationship('Description', 'CTM_Test_Suite_Description', 'id', 'test_suite_id');
      $this->addOneToManyRelationship('Plan', 'CTM_Test_Suite_Plan', 'id', 'test_suite_id');
   }

   // overloaded remove to take care of the object cleanup
   public function remove()
   {
      try {
         
         $descObj = $this->getDescription(); 
         
         if ( isset( $descObj ) ) {
            $descObj->remove();
         } 
         
         // now remove ourselves
         parent::remove(); 
      } catch ( Exception $e ) {
         throw $e;
      } 
   }

   public function setDescription( $description )
   {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $aObj = $this->getDescription();
         if ( isset( $aObj ) ) {
            $aObj->description = $description;
            $aObj->save();
         } else {
            $aObj = null;
            $aObj = new CTM_Test_Suite_Description();
            $aObj->test_suite_id = $this->id;
            $aObj->description = $description;
            $aObj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   
   public function saveRevision()
   {

      // save the revision to the revision store.
      $ctmRevisionObj = new CTM_Revision_Framework( 'suite' );
      list( $rv, $revisionId ) = $ctmRevisionObj->addRevision((integer) $this->id, $this->toXML());

      if ( $rv == true ) {
         // update the revision database tracker.
         $revObj = new CTM_Test_Suite_Revision();
         $revObj->test_suite_id = $this->id;
         $revObj->modified_at = $this->modified_at;
         $revObj->modified_by = $this->modified_by;
         $revObj->revision_id = $revisionId;
         $revObj->save();
      }

   }

   public function removePlan() {
      try {
         $testPlanSel = new CTM_Test_Suite_Plan_Selector(); 
         
         $planParams = array(
               new Light_Database_Selector_Criteria( 'test_suite_id', '=', $this->id )
         );

         $testPlans = $testPlanSel->find($planParams); 
         
         if (count($testPlans) > 0 ) {
            foreach ( $testPlans as $testPlan ) {
               $testPlan->remove();
            }
         }

      } catch ( Exception $e ) {
         throw $e;
      }
   }

}
