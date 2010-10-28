<?php

class CTM_Test_Html_Source_Parser
{

   public function parse( $htmlSource )
   {

      $results = array(); 
      $results['baseurl'] = '';
      $results['commands'] = array();

      $domDocument = new DOMDocument();
      $domDocument->loadHtml($htmlSource);

      $head = $domDocument->documentElement->getElementsByTagName('head');

      $links = $head->item(0)->getElementsByTagName('link');
      foreach ( $links as $link ) {
         $rel = $link->getAttribute('rel');
         $href = $link->getAttribute('href');
         if ( $rel == "selenium.base" ) {
            // <link rel="selenium.base" href="" />
            $results[ 'baseurl' ] = $href;
         }
      }

      $tbody = $domDocument->documentElement->getElementsByTagName('tbody');

      if ( $tbody->length > 0 ) {
         foreach ( $tbody->item(0)->childNodes as $childNode ) {
            if ( $childNode->nodeName == '#comment' ) {
               $results['commands'][] = array('#comment#', $childNode->nodeValue, '' );
            }
            if ( $childNode->nodeName == 'tr' ) {
               // iterate across the children to get the command
               $tCommand = array();
               foreach ( $childNode->childNodes as $commandBit ) {
                  if ( $commandBit->nodeName != '#text' ) {
                     $tCommand[] = $commandBit->nodeValue;
                  }
               }
               $results['commands'][] = $tCommand;
            }
         }
      }

      /*
      $testCommands = $tbody->item(0)->getElementsByTagName('tr');
      foreach ( $testCommands as $testCommand ) {
         $tds = $testCommand->getElementsByTagName('td');
         $tCommand = array();
         foreach ( $tds as $td ) {
            $tCommand[] = $td->nodeValue;
         }
         $results['commands'][] = $tCommand;
      }
      */

      return $results;

   }
}
