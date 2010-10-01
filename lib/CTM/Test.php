<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test extends Light_Database_Object
{
   public $id;
   public $testFolderId;
   public $name;
   public $testStatusId;
   public $createdAt;
   public $createdBy;
   public $modifiedAt;
   public $modifiedBy;
   public $revisionCount;

   public function init()
   {
      $this->setSqlTable('ctm_test');
      $this->setDbName('test');
      $this->addOneToOneRelationship('BaseUrl', 'CTM_Test_BaseUrl', 'id', 'testId');
      $this->addOneToOneRelationship('HtmlSource', 'CTM_Test_Html_Source', 'id', 'testId');
      $this->addOneToOneRelationship('Description', 'CTM_Test_Description', 'id', 'testId');
      $this->addOneToManyRelationship('Commands', 'CTM_Test_Command', 'id', 'testId');
      $this->addOneToManyRelationship('Params', 'CTM_Test_Param', 'id', 'testId');
      $this->addOneToManyRelationship('Revisions', 'CTM_Test_Revision', 'id', 'testId');
   }

   // overloaded remove to take care of the object cleanup
   public function remove()
   {
      // we never really allow people to remove tests.
      return;
   }

   public function setBaseUrl( $baseurl )
   {
      if ( ! isset($this->id) ) {
         return false;
      }
      try {
         $aObj = $this->getBaseUrl();
         if ( isset( $aObj ) ) {
            $aObj->baseurl = $baseurl;
            $aObj->save();
         } else {
            $aObj = null;
            $aObj = new CTM_Test_BaseUrl();
            $aObj->testId = $this->id;
            $aObj->baseurl = $baseurl;
            $aObj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
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
            $aObj = new CTM_Test_Description();
            $aObj->testId = $this->id;
            $aObj->description = $description;
            $aObj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function setHtmlSource( CTM_User $user, $htmlSource )
   {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $aObj = $this->getHtmlSource();
         if ( isset( $aObj ) ) {
            $aObj->htmlSource = $htmlSource;
            $aObj->save($user);
         } else {
            $aObj = null;
            $aObj = new CTM_Test_Html_Source();
            $aObj->testId = $this->id;
            $aObj->htmlSource = $htmlSource;
            $aObj->save($user);
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function saveRevision()
   {

      // save the revision to the revision store.
      $ctmRevisionObj = new CTM_Revision_Framework('test');
      list( $rv, $revisionId ) = $ctmRevisionObj->addRevision((integer) $this->id, $this->toXML());

      if ( $rv == true ) {
         // update the revision database tracker.
         $revObj = new CTM_Test_Revision();
         $revObj->testId = $this->id;
         $revObj->modifiedAt = $this->modifiedAt;
         $revObj->modifiedBy = $this->modifiedBy;
         $revObj->revisionId = $revisionId;
         $revObj->save();
      }

   }

}
