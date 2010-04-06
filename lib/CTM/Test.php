<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/BaseUrl.php' );
require_once( 'CTM/Test/BaseUrl/Selector.php' );
require_once( 'CTM/Test/Description.php' );
require_once( 'CTM/Test/Description/Selector.php' );
require_once( 'CTM/Test/Html/Source.php' );
require_once( 'CTM/Test/Html/Source/Selector.php' );

class CTM_Test extends Light_Database_Object {
   public $id;
   public $test_folder_id;
   public $name;
   public $test_status_id;
   public $created_at;
   public $created_by;
   public $modified_at;
   public $modified_by;

   public function init() {
      $this->setSqlTable( 'test' );
      $this->setDbName( 'test' );
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

   public function getBaseUrl() {
      if ( ! isset( $this->id ) ) {
         return null;
      } 
      try {
         $sel = new CTM_Test_BaseUrl_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $this->id ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
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

   public function getDescription() {
      if ( ! isset( $this->id ) ) {
         return null;
      } 
      try {
         $sel = new CTM_Test_Description_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $this->id ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
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

   public function getHtmlSource() {
      if ( ! isset( $this->id ) ) {
         return null;
      } 
      try {
         $sel = new CTM_Test_Html_Source_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_id', '=', $this->id ) );
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
