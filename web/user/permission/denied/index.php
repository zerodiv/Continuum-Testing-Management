<?php

require_once( '../../../../bootstrap.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_Permission_Denied extends CTM_Site { 

   public function setupPage() {
      $this->setPageTitle('Permission Denied');
      return true;
   }

   public function handleRequest() {
      $this->requiresAuth();
      return true;
   }

   public function displayBody() {
      $user_obj = $this->getUser();
      $role_obj = $user_obj->getRole();

      $this->printHtml( '<div class="aiTableContainer aiFullWidth">' );
      $this->printHtml( '<table class="ctmTable aiFullWidth">' );
      $this->printHtml( '<tr>' );
      $this->printHtml( '<th>' . $this->getSiteTitle() . ': ' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>The page you ( ' . $user_obj->username . ' ) have requested is beyond your permissions level ( ' . $role_obj->name . ' ).</td>' );
      $this->printHtml( '</tr>' );
      $this->printHtml( '</table>' );
      $this->printHtml( '</div>' );

      return true;
   }

}

$page_obj = new CTM_Site_User_Permission_Denied();
$page_obj->displayPage();
