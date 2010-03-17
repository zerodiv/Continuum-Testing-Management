<?php

require_once( 'Light/Database/Object.php' );
require_once( 'CTM/User/Factory.php' );

class CTM_User extends Light_Database_Object {
   public $id;
   public $account_role_id;
   public $username;
   public $password;
   public $display_name;
   public $is_disabled;
   public $is_verified;
   public $verified_when;
   public $created_on;
   public $temp_password;

   public function init() {
      $this->setSqlTable( 'account' );
      $this->setDbName( 'account' );
   }

   // jeo - swiped from a tutcity artical, because i am lazy today.
   // TODO: Replace this with a salt/gen password that isn't crap.
   public function generateTempPassword($len = 8) { 
      $password = '';
      $vowels = "a,o,u,e,i,y,ea,ou";
      $cons = "q,w,r,t,p,d,f,g,h,j,k,l,c,v,b,n,m,th,ch,cr,br,ch,ph,cl";
      $vowelsArray = explode(",",$vowels);
      $consArray = explode(",",$cons); 
      for ($i = 0; $i < $len/2; $i++){
         $password .= $vowelsArray[(mt_rand(0,count($vowelsArray)-1))];
         $password .= $consArray[(mt_rand(0,count($consArray)-1))];
      }
      $password = substr($password,0,$len);
      $this->temp_password = $password;
      return true;
   }

}
