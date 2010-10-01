<?php

require_once( 'Light/Database/Object.php' );

class CTM_User extends Light_Database_Object
{
   public $id;
   public $ctmUserRoleId;
   public $username;
   public $password;
   public $emailAddress;
   public $isDisabled;
   public $isVerified;
   public $verifiedWhen;
   public $createdOn;
   public $tempPassword;

   public function init()
   {
      $this->setSqlTable('ctm_user');
      $this->setDbName('user');
      $this->addOneToOneRelationship('Role', 'CTM_User_Role', 'ctmUserRoleId', 'id', true);
   }

   // jeo - swiped from a tutcity artical, because i am lazy today.
   // TODO: Replace this with a salt/gen password that isn't crap.
   public function generateTempPassword($len = 8)
   { 
      $password = '';
      $vowels = "a,o,u,e,i,y,ea,ou";
      $cons = "q,w,r,t,p,d,f,g,h,j,k,l,c,v,b,n,m,th,ch,cr,br,ch,ph,cl";
      $vowelsArray = explode(",", $vowels);
      $consArray = explode(",", $cons); 
      for ($i = 0; $i < $len/2; $i++) {
         $password .= $vowelsArray[(mt_rand(0, count($vowelsArray)-1))];
         $password .= $consArray[(mt_rand(0, count($consArray)-1))];
      }
      $password = substr($password, 0, $len);
      $this->tempPassword = $password;
      return true;
   }

}
