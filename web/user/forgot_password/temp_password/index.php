<?php
require_once( '../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );
require_once( 'Light/MVC/Url/Checksum.php' );

class CTM_Site_User_ForgotPassword_TempPassword extends CTM_Site {
   private $_urlChecksumObj;

   public function setupPage() {

      $this->setPageTitle('Forgot Password - Change Password');

      $this->_urlChecksumObj = new Light_MVC_Url_Checksum();

      return true;
   }

   public function handleRequest() {
      $action = $this->getOrPost( 'action', null );
      $id = $this->getOrPost( 'id', null );
      $password = $this->getOrPost( 'password', null );

      if ( $this->_urlChecksumObj->verify( $this, array( 'id' ) ) ) {

         if ( $action == 'save' ) {

            $user_cache_obj = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );
            $user_obj = $user_cache_obj->getById( $id );

            if ( isset( $user_obj->id ) && $user_obj->id > 0 ) {

               $user_obj->tempPassword = '';
               $user_obj->password = md5( $password );
               $user_obj->save();

               $_SESSION['user_id'] = $user_obj->id;

               header( 'Location: ' . $this->getBaseUrl() );
               return false;

            }

         }
         return true;
      }

      print_r( $_POST );
      exit();

      header( 'Location: ' . $this->getBaseUrl() . '/user/login/' );
      return false;
   }

   public function displayBody() {

      $id = $this->getOrPost( 'id', null );

      $secure_params = $this->_urlChecksumObj->create( array( 
               'id' => $id
      ), 60, false );

      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="ctmTable">' );

      $this->printHtml( '<form method="POST" action="' . $this->getBaseUrl() . '/user/forgot_password/temp_password/">' );
      foreach ( $secure_params as $n => $v ) {
         $this->printHtml( '<input type="hidden" name="' . $n . '" value="' . $v . '">' );
      }
      $this->printHtml( '<input type="hidden" name="action" value="save">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->getPageTitle() . '</th>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="odd">' );
      $this->printHtml( '<td>New password:</td>' );
      $this->printHtml( '<td><input type="password" name="password" size="40"></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '<tr class="even">' );
      $this->printHtml( '<td colspan="2"><center><input type="submit" name="Change Password"></center></td>' );
      $this->printHtml( '</tr>' );

      $this->printHtml( '</form>' );

      $this->printHtml( '</table>' );
      $this->printHtml( '</center>' );
      return true;
   }

}

$page_obj = new CTM_Site_User_ForgotPassword_TempPassword();
$page_obj->displayPage();
