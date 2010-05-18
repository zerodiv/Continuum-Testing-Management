<?php

require_once( 'Light/Config.php' );

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
      $this->_basedir = Light_Config::get( 'Light_MVC_Config', 'BASE_DIR' );
      $this->_baseurl = Light_Config::get( 'Light_MVC_Config', 'BASE_URL' );
      $this->_sitetitle = Light_Config::get( 'Light_MVC_Config', 'SITE_TITLE' );
      $this->_sessionname = Light_Config::get( 'Light_MVC_Config', 'SESSION_NAME' );

      // setup the default timezone.
      date_default_timezone_set( Light_Config::get( 'Light_MVC_Config', 'DEFAULT_TIMEZONE' ) );

      $this->_css_files = array();
      $this->_js_files = array();

      if ( is_array( Light_Config::get( 'Light_MVC_Config', 'CSS_FILES' ) ) ) {
         $this->_css_files = Light_Config::get( 'Light_MVC_Config', 'CSS_FILES' );
      }
      if ( is_array( Light_Config::get( 'Light_MVC_Config', 'JS_FILES' ) ) ) {
         $this->_js_files = Light_Config::get( 'Light_MVC_Config', 'JS_FILES' );
      }

      // init this to nothing.
      $this->_pagetitle = '';

   } 
   
   public function getOrPost( $var_name, $default_value = '', $strip_tags = true ) {
      $value = null;

      if ( isset( $_POST[ $var_name ] ) ) {
         $value = $_POST[$var_name];
      } else if ( isset( $_GET[ $var_name ] ) ) {
         $value = $_GET[ $var_name ];
      }

      if ( is_array( $value ) ) {
         return $value;
      }

      if ( isset( $value ) ) {
         if ( $strip_tags == false ) {
            return $value;
         }
         return strip_tags( $value );
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
         if ( preg_match( '/^http/', $css_file ) ) {
            $this->printHtml('<link href="' . $css_file . '" type="text/css" rel="stylesheet"/>' );
         } else {
            $this->printHtml('<link href="' . $this->_baseurl . '/css/' . $css_file . '" type="text/css" rel="stylesheet"/>' );
         }
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
      $var = stripslashes( $var );
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

   public function formatDate( $timestamp ) {
      return date( Light_Config::get( 'Light_MVC_Config', 'TIME_FORMAT' ), $timestamp );
   }

   public function isFileUploadAvailable() {
      $uploads_status = ini_get( 'file_uploads' );
      $uploads_status = trim( $uploads_status );
      if ( $uploads_status == 'On' ) {
         return true;
      }
      if ( $uploads_status == 1 ) {
         return true;
      }
      // by default we assume it's not on.
      return false;
   }

   public function maxFileUploadSize() {

      // For whatever reason the php developers allow you to pull in upload_max_filesize whenever
      // the variable for uploading is off I don't know. So instead of giving misleading results
      // we will return 0 here.
      if ( $this->isFileUploadAvailable() == false ) {
         return 0;
      }

      // upload_max_filesize
      $upload_max_filesize = ini_get( 'upload_max_filesize' );
      $upload_max_filesize = trim( $upload_max_filesize );
      if ( preg_match( '/^(\d+)G$/i', $upload_max_filesize, $pregs ) ) {
         $upload_max_filesize = (1024*1024*1024)*($pregs[1]);
      } else if ( preg_match( '/^(\d+)M$/i', $upload_max_filesize, $pregs ) ) {
         $upload_max_filesize = (1024*1024)*($pregs[1]);
      } else if ( preg_match( '/^(\d+)K$/i', $upload_max_filesize, $pregs ) ) {
         $upload_max_filesize = (1024)*($pregs[1]);
      } else if ( preg_match( '/^(\d+)$/i', $upload_max_filesize, $pregs ) ) {
         // size in bytes.
         $upload_max_filesize = $pregs[1];
      } else {
         throw new Exception( 'Failed to figure out upload max filesize from: ' . ini_get( 'upload_max_filesize' ) );
      }
      return $upload_max_filesize;
   }

}
