<?php

class CTM_Test_Html_Source_Parser {

   function __construct() {
   }

   public function parse( $html_source ) {

      $results = array(); 
      $results['baseurl'] = '';
      $results['commands'] = array();

      $dom_document = new DOMDocument();
      $dom_document->loadHtml( $html_source );

      $head = $dom_document->documentElement->getElementsByTagName( 'head' );

      $links = $head->item(0)->getElementsByTagName( 'link' );
      foreach ( $links as $link ) {
         $rel = $link->getAttribute( 'rel' );
         $href = $link->getAttribute( 'href' );
         if ( $rel == "selenium.base" ) {
            // <link rel="selenium.base" href="" />
            $results[ 'baseurl' ] = $href;
         }
      }

      $tbody = $dom_document->documentElement->getElementsByTagName( 'tbody' );

      $test_commands = $tbody->item(0)->getElementsByTagName( 'tr' );
      foreach ( $test_commands as $test_command ) {
         $tds = $test_command->getElementsByTagName( 'td' );
         $t_command = array();
         foreach ( $tds as $td ) {
            $t_command[] = $td->nodeValue;
         }
         $results['commands'][] = $t_command;
      }

      return $results;

   }
}
