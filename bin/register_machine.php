#!/usr/bin/php -q
<?php

//--------------------------------------------------------------------------------
// This is a PoC script to show how to talk to the CTM system to register
// a host with a set of browsers... this would also fire up remote control and ping back
// to the server on a regular interval
//--------------------------------------------------------------------------------
require_once( dirname( __FILE__ ) . '/../bootstrap.php' );
require_once( 'Light/CommandLine.php' );

class CTM_Register_Machine extends Light_CommandLine {
   private $_hostname;
   private $_ctm_host_id;

   public function init() {
      // init the hostname
      $this->_hostname        = null;
      $this->_hostname        = php_uname( 'n' );
      $this->_ctm_host_id     = null;
   }

   public function run() {

      if ( $this->_hostname == 'localhost' ) {
         $this->message( 'Hostname is set to localhost please provide a unique hostname.' );
         $this->done( 255 );
      }

      if ( ! isset( $this->_hostname ) ) {
         $this->message( 'Hostname was not detected please provide a unique hostname.' );
         $this->done( 255 );
      }

      $this->message( 'Hostname as detected: ' . $this->_hostname );

      $post_values = array();
      $post_values['hostname'] = $this->_hostname;

      // not super happy with PHP_OS constant atm.
      $post_values['os'] = PHP_OS;

      $browsers = array();
      $this->_findSafariBrowsers( $browsers );
      $this->_findChromeBrowsers( $browsers );
      $this->_findFirefoxBrowsers( $browsers );

      $this->message( 'browsers: ' . print_r( $browsers, true ) );

      foreach ( $browsers as $browser => $browser_version ) {
         $post_values[ $browser ] = 'yes';
         $post_values[ $browser . '_version' ] = $browser_version;
      }

      // print_r( $post_values );

      // the request will return a xml for us to work with.
      $ch = curl_init( 'http://jorcutt-laptop/et/phone/home/1.0/' );
      curl_setopt( $ch, CURLOPT_POST, true );
      curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_values );
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

      $return_xml       = curl_exec( $ch );
      $return_status    = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

      echo "return_status: $return_status\n";
      echo "return_xml: \n$return_xml\n";

   }

   private function _findSafariBrowsers(&$browsers) {
      if ( is_file( '/Applications/Safari.app/Contents/version.plist' ) ) {
         $xml = null;
         $xml = simplexml_load_file( '/Applications/Safari.app/Contents/version.plist' );
         if ( isset( $xml->dict->string[1] ) ) {
            $browsers[ 'safari' ] = (string) $xml->dict->string[1];
         }
      }
   }

   private function _findChromeBrowsers(&$browsers) {
      if ( is_dir( '/Applications/Google Chrome.app/Contents/Versions/' ) ) {
         $fds = scandir( '/Applications/Google Chrome.app/Contents/Versions/' );
         
         $high_version_id = null;
         foreach ( $fds as $f ) {
            if ( $f == '.' || $f == '..' ) {
            } else {
               list( $major, $minor, $patch ) = explode( '.', $f );
               
               if ( isset( $major ) && isset( $minor ) && isset( $patch ) ) {
                  if ( $high_version_id == null ) {
                     $high_version_id = $f;
                  } else {
                     list( $h_major, $h_minor, $h_patch ) = explode( '.', $f );
                     if ( $h_major < $major ) {
                        $high_version_id = $f;
                     } else if ( $h_major == $major && $h_minor < $minor ) {
                        $high_version_id = $f;
                     } else if ( $h_major == $major && $h_minor == $minor && $h_patch < $patch ) {
                        $high_version_id = $f;
                     }
                  }
               }
            }
         }

         if ( isset( $high_version_id ) ) {
            $browsers[ 'chrome' ] = (string) $high_version_id;
         }

      }
   }

   private function _findFirefoxBrowsers(&$browsers) {
      if ( is_file( '/Applications/Firefox.app/Contents/MacOS/updates.xml' ) ) {
        $xml = simplexml_load_file( '/Applications/Firefox.app/Contents/MacOS/updates.xml' );

        // print_r( $xml );

        if ( isset( $xml->update[0] ) ) {
           $version = null;
           foreach ( $xml->update[0]->attributes() as $f => $f_v ) {
              if ( $f == 'version' ) {
                 $version = (string) $f_v;
              }
           }
           if ( isset( $version ) ) {
              $browsers['firefox'] = $version;
           }
        }
      }
   }

}

$ctm_register_obj = new CTM_Register_Machine();
