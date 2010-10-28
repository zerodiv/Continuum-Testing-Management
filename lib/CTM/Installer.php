<?php

require_once('CTM/Site.php');
require_once('Light/Database/Connection/Factory.php');

class CTM_Installer extends CTM_Site
{
   private $_tables;

   public function setupPage()
   {
      $this->setPageTitle('Installer');
      return true;
   }

   public function handleRequest() 
   {
      // Get a write connection to the database.
      try {
         $tableDir = Light_Config::get('CTM_Config', 'base_dir') . '/sql/table';
         $dataDir = Light_Config::get('CTM_Config', 'base_dir') . '/sql/data';

         $rawTables = scandir($tableDir);
         $dbTables = array();

         $dbh = Light_Database_Connection_Factory::getDBH('test');

         if ( ! isset($dbh) ) {
            header('Content-Type: text/plain');
            echo 'Failed to connect to database, please check your db.ini';
            return false;
         }

         foreach ( $rawTables as $rawTable ) {
            $tableFile = $tableDir . '/' . $rawTable;
            $dataFile = $dataDir . '/' . $rawTable;

            if ( is_file($tableFile) ) {
               $tableName = str_replace( '.sql', '', $rawTable );

               $sth = $dbh->prepare('SHOW TABLES LIKE ?'); 
               $sth->bindParam(1, $tableName);
               $sth->execute(); 
               
               $rowCount = $sth->rowCount(); 
               
               if ( $rowCount > 0 ) {
                  $this->_tables[$tableName] = 'existed';
               } else {
                  $dbh->exec(file_get_contents($tableFile));
                  if ( is_file($dataFile) ) {
                     $dbh->exec(file_get_contents($dataFile));
                  }
                  $this->_tables[$tableName] = 'created';
               } 
            }

         }

      } catch ( Exception $e ) {
         return false;
      }
      return true;
   }

   public function displayBody()
   {
      $this->printHtml('<div class="aiTableContainer aiFullWidth">');
      $this->printHtml('<table class="ctmTable aiFullWidth">');

      $this->printhtml('<tr>');
      $this->printHtml('<th colspan="2">CTM Installer</th>');
      $this->printHtml('</tr>');

      $this->printHtml('<tr>');
      $this->printHtml('<th>Table Name:</th>');
      $this->printHtml('<th>Creation Status:</th>');
      $this->printHtml('</tr>');

      foreach ( $this->_tables as $tableName => $tableStatus ) {
         $this->printHtml('<tr class="' . $this->oddEvenClass() . '">');
         $this->printHtml('<td>' . $tableName . '</td>');
         $this->printHtml('<td><center>' . $tableStatus . '</center></td>');
         $this->printHtml('</tr>');
      }
      $this->printHtml('</table>');
      $this->printHtml('</div>');
      return true;
   }

}
