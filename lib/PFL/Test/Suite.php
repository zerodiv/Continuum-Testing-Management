<?php

class PFL_Test_Suite extends Light_DBO {
   public $id;
   public $account_id;
   public $name;
   public $description;

   public function init() {
      $this->setSqlTable( 'suite' );
   }

   public function getDBH() {
      try {
         $user_factory = new PFL_Test_Suite_Factory();
         return $user_factory->getDBH();
      } catch ( Exception $e ) {
         return null;
      }
   }

}
