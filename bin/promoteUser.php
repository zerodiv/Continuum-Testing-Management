#!/usr/bin/php -q
<?php

require_once('../bootstrap.php');
require_once('Light/CommandLine/Script.php');

require_once('CTM/User/Cache.php');
require_once('CTM/User/Role/Cache.php');

class CTM_Promote_User_CommandLine extends Light_CommandLine_Script
{
   public function run() 
   {

      $userCache = new CTM_User_Cache();
      $userRoleCache = new CTM_User_Role_Cache();
      $this->message(
          'Please enter the email address for the person ' .
          'you would like to promote to adminstrator: '
      ); 
      
      $stdinFh = fopen('php://stdin', 'r'); 
         
      $emailAddress = fgets($stdinFh);
      $emailAddress = trim($emailAddress);

      $user = $userCache->getByEmailAddress($emailAddress);

      if ( isset($user) && $user->id > 0 ) { 
         $adminRole = $userRoleCache->getByName('admin'); 
         $user->ctmUserRoleId = $adminRole->id;
         $user->save(); 
         $this->message('Granted administrator role to id: ' . $user->id); 
      } else {
         $this->message('Unable to find user by that email: ' . $emailAddress);
      } 
   }
}

$promoteUser = new CTM_Promote_User_CommandLine();
