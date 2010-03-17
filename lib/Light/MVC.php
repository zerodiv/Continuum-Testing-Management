<?php

require_once( 'Light/MVC/Config.php' );

abstract class Light_MVC {
   public $_basedir;
   public $_baseurl;
   public $_pagetitle;
   public $_sitetitle;
   public $_sessionname;
   public $_css_files;
   public $_js_files;
   
   function __construct() {
      // load the default configuration into the vars
      $this->_basedir = Light_MVC_Config::BASE_DIR();
      $this->_baseurl = Light_MVC_Config::BASE_URL();
      $this->_sitetitle = Light_MVC_Config::SITE_TITLE();
      $this->_sessionname = Light_MVC_Config::SESSION_NAME();
      if ( is_callable( 'Light_MVC_Config::CSS_FILES' ) ) {
         $this->_css_files = Light_MVC_Config::CSS_FILES();
      }
      if ( is_callable( 'Light_MVC_Config::JS_FILES' ) ) {
         $this->_js_files = Light_MVC_Config::JS_FILES();
      }

      if ( ! is_array( $this->_css_files ) ) {
         $this->_css_files = array();
      }

      if ( ! is_array( $this->_js_files ) ) {
         $this->_js_files = array();
      }

      // init this to nothing.
      $this->_pagetitle = '';
   } 
   
   public function getOrPost( $var_name, $default_value = '' ) {
      if ( isset( $_POST[ $var_name ] ) ) {
         return strip_tags($_POST[ $var_name ]);
      }
      if ( isset( $_GET[ $var_name ] ) ) {
         return strip_tags($_GET[$var_name]);
      }
      return $default_value;
   } 
   
   public function displayPage() {

      $action_stack = array( 
            'setupPage',
            'setupSession',
            'handleRequest',
            'displayHeader', 
            'displayBody',
            'displayFooter',
            ); 
      
      foreach ( $action_stack as $action ) {
         if ( $this->$action() == false ) {
            return; 
         }
      } 
   }

   public function debugPage() {
      echo "<pre>";
      print_r( $GLOBALS );
      echo "</pre>";
   }

   public function setupPage() {
      return true;
   } 
   
   public function setupSession() {
      // this function really should not be overridden
      session_name( $this->_sessionname );
      session_start();
      return true;
   } 
   
   public function handleRequest() {
      return true;
   } 
   
   public function displayHeader() {
      $this->printHtml('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' );
      $this->printHtml('<html>');
      $this->printHtml('<head>');

      $title_string = $this->_sitetitle;
      if ( isset( $this->_pagetitle) ) {
         $title_string .= ' - ' . $this->_pagetitle;
      }

      $this->printHtml( '<title>' . $title_string .  '</title>' );

      $this->displayHeader_CSS();
      $this->displayHeader_JavaScript();

      $this->printHtml( '</head>' );
      $this->printHtml( '<body>' );
      return true;
   }

   public function displayHeader_CSS() {
      foreach ( $this->_css_files as $css_file ) {
         $this->printHtml('<link href="' . $this->_baseurl . '/css/' . $css_file . '" type="text/css" rel="stylesheet"/>' );
      }
      return true;
   }

   public function displayHeader_JavaScript() {
      foreach ( $this->_js_files as $js_file ) {
         $this->printHtml('<script type="text/javascript" src="' . $this->_baseurl . '/js/' . $js_file . '"></script>' );
      }
      return true;
   }
   
   public function displayBody() {
      echo "<!-- this should be overridden -->\n";
      return true;
   } 
   
   public function displayFooter() {
      $this->printHtml( '</body>' );
      $this->printHtml( '</html>' );
      return true;
   } 
   
   public function requiresAuth() {
      if ( $this->isLoggedIn() != true ) {
         header( 'Location: ' . $this->_baseurl . '/user/login' );
         exit();
      } 
      return true; 
   } 
   
   public function isLoggedIn() {
      if ( isset( $_SESSION['user'] ) && $_SESSION['user']->id > 0 ) {
         return true;
      }
      return false;
   } 
  
   // yes you need to use this dammit.
   public function escapeVariable( $var ) {
      return htmlentities( $var, ENT_QUOTES, 'UTF-8' );
   }

   public function printHtml( $html ) {
      echo $html . "\n";
   }

   public function printJs( $js ) {
      echo '<script language="JavaScript">' . "\n";
      echo $js;
      echo '</script>' . "\n";
   }


}

