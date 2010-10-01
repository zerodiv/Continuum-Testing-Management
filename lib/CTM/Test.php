<?php

require_once( 'Light/Database/Object.php' );

class CTM_Test extends Light_Database_Object {
   public $id;
   public $testFolderId;
   public $name;
   public $test_status_id;
   public $created_at;
   public $created_by;
   public $modified_at;
   public $modified_by;
   public $revision_count;

   public function init() {
      $this->setSqlTable( 'test' );
      $this->setDbName( 'test' );
      $this->addOneToOneRelationship( 'BaseUrl', 'CTM_Test_BaseUrl', 'id', 'test_id' );
      $this->addOneToOneRelationship( 'HtmlSource', 'CTM_Test_Html_Source', 'id', 'test_id' );
      $this->addOneToOneRelationship( 'Description', 'CTM_Test_Description', 'id', 'test_id' );
      $this->addOneToManyRelationship( 'Commands', 'CTM_Test_Command', 'id', 'test_id' );
      $this->addOneToManyRelationship( 'Params', 'CTM_Test_Param', 'id', 'test_id' );
      $this->addOneToManyRelationship( 'Revisions', 'CTM_Test_Revision', 'id', 'test_id' );
   }

   // overloaded remove to take care of the object cleanup
   public function remove() {
      try {

         $base_url_obj = $this->getBaseUrl();
         
         if ( isset( $base_url_obj ) ) {
            $base_url_obj->remove();
         }

         $desc_obj = $this->getDescription(); 
         
         if ( isset( $desc_obj ) ) {
            $desc_obj->remove();
         } 
         
         $html_source_obj = $this->getHtmlSource(); 
         
         if ( isset( $html_source_obj ) ) {
            $html_source_obj->remove();
         } 

         // test command removal
         $test_commands = $this->getCommands();
         if ( count( $test_commands ) > 0 ) {
            foreach ( $test_commands as $test_command ) {
               $test_command->remove();
            }
         }

         // test params removal
         $test_params = $this->getParams();
         if ( count( $test_params ) > 0 ) {
            foreach ( $test_params as $test_param ) {
               $test_param->remove();
            }
         }

         // test revisions removal
         $test_revisions = $this->getRevisions();
         if ( count( $test_revisions ) > 0 ) {
            foreach ( $test_revisions as $test_revision ) {
               $test_revision->remove();
            }
         }

         // now remove ourselves
         parent::remove(); 

      } catch ( Exception $e ) {
         throw $e;
      } 
   }

   public function setBaseUrl( $baseurl ) {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $a_obj = $this->getBaseUrl();
         if ( isset( $a_obj ) ) {
            $a_obj->baseurl = $baseurl;
            $a_obj->save();
         } else {
            $a_obj = null;
            $a_obj = new CTM_Test_BaseUrl();
            $a_obj->test_id = $this->id;
            $a_obj->baseurl = $baseurl;
            $a_obj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
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
            $a_obj = new CTM_Test_Description();
            $a_obj->test_id = $this->id;
            $a_obj->description = $description;
            $a_obj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function setHtmlSource( CTM_User $user, $html_source ) {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $a_obj = $this->getHtmlSource();
         if ( isset( $a_obj ) ) {
            $a_obj->html_source = $html_source;
            $a_obj->save( $user );
         } else {
            $a_obj = null;
            $a_obj = new CTM_Test_Html_Source();
            $a_obj->test_id = $this->id;
            $a_obj->html_source = $html_source;
            $a_obj->save( $user );
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function saveRevision() {

      // save the revision to the revision store.
      $ctm_revision_obj = new CTM_Revision_Framework( 'test' );
      list( $rv, $revision_id ) = $ctm_revision_obj->addRevision( (integer) $this->id, $this->toXML() );

      if ( $rv == true ) {
         // update the revision database tracker.
         $rev_obj = new CTM_Test_Revision();
         $rev_obj->test_id = $this->id;
         $rev_obj->modified_at = $this->modified_at;
         $rev_obj->modified_by = $this->modified_by;
         $rev_obj->revision_id = $revision_id;
         $rev_obj->save();
      }

   }

}
