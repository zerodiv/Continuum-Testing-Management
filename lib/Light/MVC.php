<?php

require_once( 'Light/Config.php' );

abstract class Light_MVC
{
   private $_baseURL;
   private $_pageTitle;
   private $_siteTitle;
   private $_sessionName;
   private $_cssFiles;
   private $_jsFiles;

   function __construct()
   {

      // setup the default timezone.
      date_default_timezone_set(Light_Config::get('Light_MVC_Config', 'DEFAULT_TIMEZONE'));

      // load the default configuration into the vars
      $this->setBaseUrl();
      $this->setPageTitle('');
      $this->setSiteTitle(null);
      $this->setSessionName();
      $this->setCssFiles();
      $this->setJsFiles();

   } 
   
   public function getBaseUrl()
   {
      return $this->_baseURL;
   }

   public function setBaseUrl()
   {
      if ( isset($this->_baseURL) ) {
         return;
      }
      $this->_baseURL = Light_Config::get('Light_MVC_Config', 'BASE_URL');
   }

   public function getPageTitle()
   {
      return $this->_pageTitle;
   }

   public function setPageTitle($title)
   {
      $this->_pageTitle = $title;
   }

   public function getSiteTitle()
   {
      return $this->_siteTitle;
   }

   public function setSiteTitle($title)
   {
      if (isset($this->_siteTitle)) {
         $this->_siteTitle = $title;
         return;
      }
      $this->_siteTitle = Light_Config::get('Light_MVC_Config', 'SITE_TITLE');
   }

   public function getSessionName()
   {
      return $this->_sessionName;
   }

   public function setSessionName()
   {
      if (isset($this->_sessionName)) {
         return;
      }
      $this->_sessionName = Light_Config::get('Light_MVC_Config', 'SESSION_NAME');
   }

   public function getCssFiles() 
   {
      return $this->_cssFiles;
   }

   public function setCssFiles()
   {

      if (isset($this->_cssFiles)) {
         return;
      }

      $this->_cssFiles = array();

      if (is_array(Light_Config::get('Light_MVC_Config', 'CSS_FILES'))) {
         $this->_cssFiles = Light_Config::get('Light_MVC_Config', 'CSS_FILES');
      }

   }

   public function getJsFiles()
   {
      return $this->_jsFiles;
   }

   public function setJsFiles()
   {

      if (isset($this->_jsFiles)) {
         return;
      }

      $this->_jsFiles = array();

      if (is_array(Light_Config::get('Light_MVC_Config', 'JS_FILES'))) {
         $this->_jsFiles = Light_Config::get('Light_MVC_Config', 'JS_FILES');
      }

   }

   public function getOrPost($varName, $defaultValue = '', $stripTags = true)
   {
      $value = null;

      if ( isset( $_POST[ $varName ] ) ) {
         $value = $_POST[$varName];
      } else if ( isset( $_GET[ $varName ] ) ) {
         $value = $_GET[ $varName ];
      }

      if (is_array($value)) {
         return $value;
      }

      if ( isset( $value ) ) {
         if ( $stripTags == false ) {
            return $value;
         }
         return strip_tags($value);
      }

      return $defaultValue;
   } 

   public function displayPage()
   {

      $actionStack = array( 
            'setupPage',
            'setupSession',
            'handleRequest',
            'displayHeader', 
            'displayBody',
            'displayFooter',
            ); 
      
      foreach ( $actionStack as $action ) {
         if ( $this->$action() == false ) {
            return; 
         }
      } 
   }

   public function debugPage()
   {
      echo "<pre>";
      print_r($GLOBALS);
      echo "</pre>";
   }

   public function setupPage()
   {
      return true;
   } 
   
   public function setupSession()
   {
      // this function really should not be overridden
      session_name($this->getSessionName());
      session_start();
      return true;
   } 
   
   public function handleRequest()
   {
      return true;
   } 
   
   public function displayHeader()
   {
      $this->printHtml('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">');
      $this->printHtml('<html>');
      $this->printHtml('<head>');

      $titleString = $this->getSiteTitle();
      if ( $this->getPageTitle() != '' ) {
         $titleString .= ' - ' . $this->getPageTitle();
      }

      $this->printHtml('<title>' . $titleString .  '</title>');

      $this->displayHeader_CSS();
      $this->displayHeader_JavaScript();

      $this->printHtml('</head>');
      $this->printHtml('<body>');
      return true;
   }

   public function displayHeader_CSS()
   {
      foreach ( $this->getCssFiles() as $cssFile ) {
         if ( preg_match('/^http/', $cssFile) ) {
            $this->printHtml('<link href="' . $cssFile . '" type="text/css" rel="stylesheet"/>');
         } else {
            $this->printHtml(
                '<link href="' . $this->getBaseUrl() . '/css/' . $cssFile . '" type="text/css" rel="stylesheet"/>'
            );
         }
      }
      return true;
   }

   public function displayHeader_JavaScript()
   {
      foreach ( $this->getJsFiles() as $jsFile ) {
         $this->printHtml(
             '<script type="text/javascript" src="' . $this->getBaseUrl() . '/js/' . $jsFile . '"></script>'
         );
      }
      return true;
   }
   
   public function displayBody()
   {
      echo "<!-- this should be overridden -->\n";
      return true;
   } 
   
   public function displayFooter()
   {
      $this->printHtml('</body>');
      $this->printHtml('</html>');
      return true;
   } 
   
   public function requiresAuth()
   {
      if ( $this->isLoggedIn() != true ) {
         header('Location: ' . $this->getBaseUrl() . '/user/login');
         exit();
      } 
      return true; 
   } 
   
   public function isLoggedIn()
   {
      if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] > 0 ) {
         return true;
      }
      return false;
   } 
  
   // yes you need to use this dammit.
   public function escapeVariable( $var )
   {
      $var = stripslashes($var);
      return htmlentities($var, ENT_QUOTES, 'UTF-8');
   }

   public function printHtml( $html )
   {
      echo $html . "\n";
   }

   public function printJs( $js )
   {
      echo '<script language="JavaScript">' . "\n";
      echo $js;
      echo '</script>' . "\n";
   }

   public function formatDate( $timestamp )
   {
      return date(Light_Config::get('Light_MVC_Config', 'TIME_FORMAT'), $timestamp);
   }

   public function isFileUploadAvailable()
   {
      $uploadsStatus = ini_get('file_uploads');
      $uploadsStatus = trim($uploadsStatus);
      if ( $uploadsStatus == 'On' ) {
         return true;
      }
      if ( $uploadsStatus == 1 ) {
         return true;
      }
      // by default we assume it's not on.
      return false;
   }

   public function maxFileUploadSize()
   {

      // For whatever reason the php developers allow you to pull in upload_max_filesize whenever
      // the variable for uploading is off I don't know. So instead of giving misleading results
      // we will return 0 here.
      if ( $this->isFileUploadAvailable() == false ) {
         return 0;
      }

      // upload_max_filesize
      $uploadMaxFilesize = ini_get('upload_max_filesize');
      $uploadMaxFilesize = trim($uploadMaxFilesize);
      if ( preg_match('/^(\d+)G$/i', $uploadMaxFilesize, $pregs) ) {
         $uploadMaxFilesize = (1024*1024*1024)*($pregs[1]);
      } else if ( preg_match('/^(\d+)M$/i', $uploadMaxFilesize, $pregs) ) {
         $uploadMaxFilesize = (1024*1024)*($pregs[1]);
      } else if ( preg_match('/^(\d+)K$/i', $uploadMaxFilesize, $pregs) ) {
         $uploadMaxFilesize = (1024)*($pregs[1]);
      } else if ( preg_match('/^(\d+)$/i', $uploadMaxFilesize, $pregs) ) {
         // size in bytes.
         $uploadMaxFilesize = $pregs[1];
      } else {
         throw new Exception( 'Failed to figure out upload max filesize from: ' . ini_get('upload_max_filesize') );
      }
      return $uploadMaxFilesize;
   }

}
