<?php

require_once( '../../../bootstrap.php' );
require_once( 'CTM/Site.php' );
require_once( 'CTM/Test.php' );

class CTM_Site_Test_Add extends CTM_Site
{

   public function setupPage()
   {
      $this->setPageTitle('Add Test');
      return true;
   }

   public function handleRequest()
   {

      $this->requiresAuth();
      $this->requiresRole(array( 'user', 'qa', 'admin' ));
      
      $testFolderId   = $this->getOrPost('testFolderId', '');
      $name             = $this->getOrPost('name', '');
      $description      = $this->getOrPost('description', '');

      if ( $name == '' ) {
         return true;
      }

      $userObj = $this->getUser();
      $roleObj = $userObj->getRole();

      if ( $roleObj->name == 'user' ) {
         $userFolder = $this->getUserFolder();
         if ( $testFolderId != $userFolder->id ) {
            header('Location: ' . $this->getBaseUrl() . '/user/permission/denied/');
            return false;
         }
      }

      $htmlSource = null;

      $htmlSourceFile = $_FILES['htmlSourceFile']['tmp_name'];

      if ( isset($htmlSourceFile) && filesize($htmlSourceFile) > 0 ) {
         $htmlSource = file_get_contents($htmlSourceFile);
      }

      // no html file - skip me!
      if ( ! isset( $htmlSource ) ) {
         return true;
      }

      try {

         // create the test.
         $new = new CTM_Test();
         $new->testFolderId = $testFolderId;
         $new->name = $name;
         $new->testStatusId = 1; // all tests are created in a pending state.
         $createAt = time(); // yes i know this is paranoia
         $new->createdAt = $createAt;
         $new->createdBy = $userObj->id;
         $new->modifiedAt = $createAt;
         $new->modifiedBy = $userObj->id;
         $new->revisionCount = 1;
         $new->save();

         if ( $new->id > 0 ) {

            // add the html source.
            $new->setHtmlSource($userObj, $htmlSource);

            // add the description.
            $new->setDescription($description);

         }

         // save the inital version
         $new->saveRevision();
      
         header('Location: ' . $this->getBaseUrl() . '/tests/?parentId=' . $testFolderId);
         return false;

      } catch ( Exception $e ) {
         // failed to insert.
         return true;
      }

      // added our child send us back to our parent
      header('Location: ' . $this->getBaseUrl() . '/test/tests/?parentId=' . $testFolderId);
      return false;

   }
                           

   public function displayBody()
   {
      $testFolderId   = $this->getOrPost('testFolderId', '');
      $name             = $this->getOrPost('name', '');
      $description      = $this->getOrPost('description', '');

      $this->printHtml('<center>');

      $this->printHtml('<table>');

      $this->printHtml('<tr>');
      $this->printHtml('<td valign="top">');
      $this->printHtml('<table class="ctmTable">');
      $this->printHtml(
          '<form enctype="multipart/form-data" method="POST" action="' . $this->getBaseUrl() . '/test/add/">'
      );
      $this->printHtml('<input type="hidden" value="' . $testFolderId . '" name="testFolderId">');

      $this->printHtml('<tr>');
      $this->printHtml('<th colspan="4">Add Test</th>');
      $this->printHtml('</td>');
      $this->printHtml('</tr>');

      $this->printHtml('<tr>');
      $this->printHtml('<td class="odd">Name:</td>');
      $this->printHtml(
          '<td class="odd"><input type="text" name="name" size="30" value="' . $this->escapeVariable($name) . '"></td>'
      );
      $this->printHtml('</tr>');

      $this->printHtml('<tr>');
      $this->printHtml('<td class="odd">Folder:</td>');
      $this->printHtml(
          '<td class="odd">' . $this->_fetchFolderPath($this->getBaseUrl() . '/tests/', $testFolderId) . '</td>'
      );
      $this->printHtml('</tr>');

      $this->printHtml('<tr>');
      $this->printHtml('<td class="odd" colspan="2">Description:</td>');
      $this->printHtml('</tr>');
      $this->printHtml('<tr>');
      $this->printHtml(
          '<td class="odd" colspan="2"><textarea name="description" rows="25" cols="60">' . 
          $this->escapeVariable($description) . 
          '</textarea></td>'
      );
      $this->printHtml('</tr>');

      if ( $this->isFileUploadAvailable() ) {

         $this->printHtml('<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxFileUploadSize() . '">');
      
         $this->printHtml('<tr>');
         $this->printHtml('<td class="odd">File:</td>');
         $this->printHtml('<td class="odd"><input type="file" name="htmlSourceFile"></td>');
         $this->printHtml('</tr>');

      }

      $this->printHtml('<tr>');
      $this->printHtml('<td colspan="2" class="even"><center><input type="submit" value="Add"></center></td>');
      $this->printHtml('</tr>');

      $this->printHtml('</form>');

      $this->printHtml('</table>');
      $this->printHtml('</td>');
      $this->printHtml('</tr>');

      $this->printHtml('</table>');
      $this->printHtml('</center>');

      return true;
   }

}

$testAddObj = new CTM_Site_Test_Add();
$testAddObj->displayPage();
