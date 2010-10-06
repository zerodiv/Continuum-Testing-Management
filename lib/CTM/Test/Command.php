<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/Command/Target.php' );
require_once( 'CTM/Test/Command/Target/Selector.php' );
require_once( 'CTM/Test/Command/Value.php' );
require_once( 'CTM/Test/Command/Value/Selector.php' );

class CTM_Test_Command extends Light_Database_Object
{
   public $id;
   public $testId;
   public $testSeleniumCommandId;
   public $testParamLibraryId;

   public function init()
   {
      $this->setSqlTable('ctm_test_command');
      $this->setDbName('test');
      $this->addOneToOneRelationship('SeleniumCommand', 'CTM_Test_Selenium_Command', 'testSeleniumCommandId', 'id');
      $this->addOneToOneRelationship('ParamLibrary', 'CTM_Test_Param_Library', 'testParamLibraryId', 'id');
   }

   // overloaded remove to take care of the object cleanup
   public function remove()
   {

      try { 

         $target = $this->getTarget();

         if ( isset($target) ) {
            $target->remove();
         }

         $value = $this->getValue();

         if ( isset($value) ) {
            $value->remove();
         }

         // now remove ourselves
         parent::remove();

      } catch ( Exception $e ) {
         throw $e;
      }

   }

   public function setTarget( $target )
   {
      if ( ! isset($this->id) ) {
         return false;
      }
      try {
         $sel = new CTM_Test_Command_Target_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testCommandId', '=', $this->id ) );
         $rows = $sel->find($andParams);
         if ( isset( $rows[0] ) ) {
            $aObj = $rows[0];
            $aObj->target = $target;
            $aObj->save();
         } else {
            $aObj = new CTM_Test_Command_Target();
            $aObj->testCommandId = $this->id;
            $aObj->target = $target;
            $aObj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getTarget()
   {
      if ( ! isset( $this->id ) ) {
         return null;
      } 
      try {
         $sel = new CTM_Test_Command_Target_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testCommandId', '=', $this->id ) );
         $rows = $sel->find($andParams);
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }

   public function setValue( $value )
   {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $sel = new CTM_Test_Command_Value_Selector();
         $andParams = array(new Light_Database_Selector_Criteria( 'testCommandId', '=', $this->id));
         $rows = $sel->find($andParams);
         if ( isset( $rows[0] ) ) {
            $aObj = $rows[0];
            $aObj->value = $target;
            $aObj->save();
         } else {
            $aObj = new CTM_Test_Command_Value();
            $aObj->testCommandId = $this->id;
            $aObj->value = $value;
            $aObj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getValue()
   {
      if ( ! isset( $this->id ) ) {
         return null;
      }
      try {
         $sel = new CTM_Test_Command_Value_Selector();
         $andParams = array( new Light_Database_Selector_Criteria( 'testCommandId', '=', $this->id ) );
         $rows = $sel->find($andParams);
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }

}
