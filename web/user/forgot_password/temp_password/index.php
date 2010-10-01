<?php
require_once( '../../../../bootstrap.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );
require_once( 'CTM/Site.php' );

class CTM_Site_User_ForgotPassword_TempPassword extends CTM_Site {

   public function setupPage() {
      $this->_pagetitle = 'Forgot Password - Change Password';
      return true;
   }

   public function handleRequest() {
      $action = $this->getOrPost( 'action', null );
      $id = $this->getOrPost( 'id', null );
      $password = $this->getOrPost( 'password', null );

      if ( $this->Url_Checksum->verify( $this, array( 'id' ) ) ) {

         if ( $action == 'save' ) {

            $user_cache_obj = Light_Database_Object_Cache_Factory::factory( 'CTM_User_Cache' );
            $user_obj = $user_cache_obj->getById( $id );

            if ( isset( $user_obj->id ) && $user_obj->id > 0 ) {

               $user_obj->tempPassword = '';
               $user_obj->password = md5( $password );
               $user_obj->save();

               $_SESSION['user_id'] = $user_obj->id;

               header( 'Location: ' . $this->_baseurl );
               return false;

            }

         }
         return true;
      }

      print_r( $_POST );
      exit();

      header( 'Location: ' . $this->_baseurl . '/user/login/' );
      return false;
   }

   public function displayBody() {

      $id = $this->getOrPost( 'id', null );

      $secure_params = $this->Url_Checksum->create( array( 
               'id' => $id
      ), 60, false );

      $this->printHtml( '<center>' );
      $this->printHtml( '<table class="ctmTable">' );

      $this->printHtml( '<form method="POST" action="' . $this->_baseurl . '/user/forgot_password/tempPassword/">' );
      foreach ( $secure_params as $n => $v ) {
         $this->printHtml( '<input type="hidden" name="' . $n . '" value="' . $v . '">' );
      }
      $this->printHtml( '<input type="hidden" name="action" value="save">' );

      $this->printHtml( '<tr>' );
      $this->printHtml( '<th colspan="2">' . $this->_pagetitle . '</th>' );
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
