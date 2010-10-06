<?php

require_once( 'Light/Database/Object.php' );

require_once( 'CTM/Test/Run/Command/Target.php' );
require_once( 'CTM/Test/Run/Command/Target/Selector.php' );
require_once( 'CTM/Test/Run/Command/Value.php' );
require_once( 'CTM/Test/Run/Command/Value/Selector.php' );

class CTM_Test_Run_Command extends Light_Database_Object {
   public $id;
   public $testRunId;
   public $test_suite_id;
   public $testId;
   public $testSeleniumCommandId;
   public $test_param_library_id;

   public function init() {
      $this->setSqlTable( 'test_run_command' );
      $this->setDbName( 'test' );
   }

   // overloaded remove to take care of the object cleanup
   public function remove() {

      try { 

         $target = $this->getTarget();

         if ( isset( $target ) ) {
            $target->remove();
         }

         $value = $this->getValue();

         if ( isset( $value ) ) {
            $value->remove();
         }

         // now remove ourselves
         parent::remove();

      } catch ( Exception $e ) {
         throw $e;
      }

   }

   public function setTarget( $target ) {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $a_obj = $this->getTarget();
         if ( isset( $a_obj ) ) {
            $a_obj->target = $target;
            $a_obj->save();
         } else {
            $a_obj = new CTM_Test_Run_Command_Target();
            $a_obj->test_run_command_id = $this->id;
            $a_obj->target = $target;
            $a_obj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getTarget() {
      if ( ! isset( $this->id ) ) {
         return null;
      } 
      try {
         $sel = new CTM_Test_Run_Command_Target_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_run_command_id', '=', $this->id ) );
         $rows = $sel->find( $and_params );
         if ( isset( $rows[0] ) ) {
            return $rows[0];
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return null;
   }

   public function setValue( $value ) {
      if ( ! isset( $this->id ) ) {
         return false;
      }
      try {
         $a_obj = $this->getValue();
         if ( isset( $a_obj ) ) {
            $a_obj->value = $value;
            $a_obj->save();
         } else {
            $a_obj = new CTM_Test_Run_Command_Value();
            $a_obj->test_run_command_id = $this->id;
            $a_obj->value = $value;
            $a_obj->save();
         }
      } catch ( Exception $e ) {
         throw $e;
      }
      return false;
   }

   public function getValue() {
      if ( ! isset( $this->id ) ) {
         return null;
      }
      try {
         $sel = new CTM_Test_Run_Command_Value_Selector();
         $and_params = array( new Light_Database_Selector_Criteria( 'test_run_command_id', '=', $this->id ) );
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
