<?php

require_once( 'Light/MVC.php' );
require_once( 'Light/Database/Object/Cache/Factory.php' );

// we have to include the user object to thaw it from the session
require_once( 'CTM/User.php' );
require_once( 'CTM/Test/Folder/Cache.php' );
require_once( 'CTM/Test/Suite/Folder/Cache.php' );

class CTM_Site extends Light_MVC
{
    private $_oddEvenClass;

    public function displayHeader()
    {

        // display the normal header.
        parent::displayHeader();

        $this->printHtml('<div class="aiMainContent clearfix">');

        $this->printHtml('<div class="aiTopNav">');

        if ( $this->isLoggedIn() ) {

            $userObj = $this->getUser();
            $roleObj = $userObj->getRole();

            $this->printHtml('<!-- role: ' . $roleObj->name . ' -->');

            $this->printHtml('<ul class="basictab">');
            $this->printHtml('<li><a href="' . $this->getBaseUrl() . '">' . $this->getSiteTitle() . '</a></li>');

            $allowedRoles = array( 'user', 'qa', 'admin' );
            if ( in_array($roleObj->name, $allowedRoles) ) {
                $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/test/suites/">Suites</a></li>');
            }

            $allowedRoles = array( 'user', 'qa', 'admin' );
            if ( in_array($roleObj->name, $allowedRoles) ) {
                $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/tests/">Tests</a></li>');
            }

            $allowedRoles = array( 'qa', 'admin' );
            if ( in_array($roleObj->name, $allowedRoles) ) {
                // $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/folders/">Folders</a></li>');
            }

            $allowedRoles = array( 'qa', 'admin' );
            if ( in_array($roleObj->name, $allowedRoles) ) {
                $this->printHtml(
                        '<li><a href="' . $this->getBaseUrl() . '/test/param/library/">' .
                        'Parameter Library' .
                        '</a></li>'
                        );
            }

            $allowedRoles = array( 'qa', 'admin' );
            if ( in_array($roleObj->name, $allowedRoles) ) {
                $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/test/runs/">Runs</a></li>');
            }


            $this->printHtml(
                    '<li><a href="' . $this->getBaseUrl() . '/user/logout/">' .
                    'Logout : ' . $this->escapeVariable($userObj->username) . 
                    '</a></li>'
                    );

            $this->printHtml('</ul>');

            if ( $this->isLoggedIn() && $roleObj->name == 'admin' ) {
                $this->printHtml('<ul class="basictab">');
                $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/user/manager/">Manage Users</a></li>');
                $this->printHtml('<li><a href="' . $this->getBaseUrl(). '/test/machines/">Machines</a></li>');
                $this->printHtml('</ul>');
            }
        } else {
            $this->printHtml('<ul class="basictab">');
            $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/user/login/">Login</a></li>');
            $this->printHtml('<li><a href="' . $this->getBaseUrl() . '/user/create/">Create Account</a></li>');
            $this->printHtml('</ul>');
        }


        $this->printHtml('</div>');

        return true;
    }

    public function displayFooter()
    {

        $this->printHtml('</div>');

        parent::displayFooter();

        return true;
    }

    public function requiresAuth()
    {
        if ( $this->isLoggedIn() == true ) {
            return true; 
        } 
        header('Location: ' . $this->getBaseUrl() . '/user/login');
        exit();
    } 

    public function requiresRole( $acceptableRoles )
    {
        if ( is_array($acceptableRoles) && count($acceptableRoles) > 0 ) {
            $user = $this->getUser();
            if ( isset( $user ) ) {
                $currentRole = $user->getRole();
                // if their role is in a acceptable role list then we are good for this page.
                if ( isset($currentRole) && in_array($currentRole->name, $acceptableRoles) ) {
                    return true;
                }
            }
        }
        header('Location: ' . $this->getBaseUrl() . '/user/permission/denied/');
        exit();
    }

    public function isLoggedIn()
    {
        if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] > 0 ) {
            $userObj = $this->getUser();
            // a user cannot be logged in if they are disabled.
            if ( $userObj->isDisabled == true ) {
                return false;
            }
            // a user cannot be logged in if they are not verified.
            if ( $userObj->isVerified != true ) {
                return false;
            }
            return true;
        }
        return false;
    } 

    public function getUser()
    {
        if ( isset( $_SESSION['user_id'] ) && $_SESSION['user_id'] > 0 ) {
            $userCache = Light_Database_Object_Cache_Factory::factory('CTM_User_Cache');
            $userObj = $userCache->getById($_SESSION['user_id']);
            return $userObj;
        }
        return null;
    }

    public function getUserFolder()
    {
        $userObj = $this->getUser();
        if ( isset($userObj) ) {
            // okay we have a user object, try to lookup folders by name
            $folderCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Folder_Cache');
            $userFolder = $folderCache->getByName('CTM-Users');

            // create the CTM-User folder.
            if ( ! isset( $userFolder ) ) {
                $userFolder = new CTM_Test_Folder();
                $userFolder->parentId = 1;
                $userFolder->name = 'CTM-Users';
                $userFolder->save();

                if ( ! isset( $userFolder->id ) || empty( $userFolder->id ) ) {
                    return null;
                }
            }

            $childFolder = $folderCache->getChildByName($userFolder->id, $userObj->id);

            if ( isset($childFolder) ) {
                return $childFolder;
            }

            $childFolder = new CTM_Test_Folder();
            $childFolder->parentId = $userFolder->id;
            $childFolder->name = $userObj->id;
            $childFolder->save();

            if ( $childFolder->id > 0 ) {
                return $childFolder;
            }

            return null;
        }

        return null;
    }

    public function oddEvenReset()
    {
        $this->_oddEvenClass = null;
    }

    public function oddEvenClass()
    {
        if ( $this->_oddEvenClass == 'odd' ) {
            $this->_oddEvenClass = 'even';
        } else if ( $this->_oddEvenClass == 'even' ) {
            $this->_oddEvenClass = 'odd';
        } else {
            $this->_oddEvenClass = 'odd';
        }
        return $this->_oddEvenClass;
    }

    public function cleanupUserName( $username )
    {
        $username = strtolower($username);
        $username = ltrim($username);
        $username = rtrim($username);
        return $username;
    }

    public function _fetchFolderPath( $currentBaseurl, $parentId, $isSuite = false )
    {

        $folderCache = null;
        if ( $isSuite == true ) {
            $folderCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Suite_Folder_Cache');
        } else {
            $folderCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Folder_Cache');
        }
        $parents = array(); 
        $folderCache->getFolderParents($parentId, $parents);
        $parents = array_reverse($parents);
        $parentsCnt = count($parents);

        $folderPath = '';
        $previousParent = null;
        foreach ( $parents as $parent ) {
            $folderPath .= '/';
            $folderPath .= 
                '<a href="' . $currentBaseurl . '?parentId=' . $parent->id . '">' .
                $this->escapeVariable($parent->name) . 
                '</a>';
            $previousParent = $parent;
        }

        return $folderPath;
    }

    public function _displayFolderBreadCrumb( $currentBaseurl, $parentId = 0, $isSuite = false )
    {

        $userObj = $this->getUser();
        $roleObj = $userObj->getRole();

        $folderPath = $this->_fetchFolderPath($currentBaseurl, $parentId, true);

        $folderCache = null;
        $newFolderText = null;
        if ( $isSuite == true ) {
            $folderCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Suite_Folder_Cache');
            $newFolderText = 'New Suite Folder';
        } else {
            $folderCache = Light_Database_Object_Cache_Factory::factory('CTM_Test_Folder_Cache');
            $newFolderText = 'New Test Folder';
        }

        $children = array();
        $children = $folderCache->getFolderChildren($parentId); 

        $this->printHtml('<div class="aiTableContainer aiFullWidth">');
        $this->printHtml('<table class="ctmTable aiFullWidth">');
        $this->printHtml('<tr class="odd">');
        $this->printHtml('<td>Current folder path: ' .  $folderPath . '</td>');

        if ( count($children) > 0 ) {
            $this->printHtml('<form action="' . $currentBaseurl . '" method="POST">');
            $this->printHtml('<td><center>');
            $this->printHtml('Switch to Sub Folder: ');
            $this->printHtml('<select name="parentId">');
            $this->printHtml('<option value="0">Pick a sub-folder</option>');
            foreach ( $children as $child ) {
                $this->printHtml(
                        '<option value="' . $child->id . '">' . 
                        $this->escapeVariable($child->name) . 
                        '</option>'
                        );
            }
            $this->printHtml('</select>');
            $this->printHtml('<input type="submit" value="Go!">');
            $this->printHtml(
                    '&nbsp;' .
                    '<a href="' . $this->getBaseUrl() . '/test/folder/add/?parentId=' . $parentId . '&isSuite=' . $isSuite . '" class="ctmButton">' .
                    $newFolderText .
                    '</a>'
                    );
            $this->printHtml('</center></td>');
            $this->printHtml('</form>');

        } else {
            $this->printHtml('<td><center>');
            $this->printHtml(
                    '<a href="' . $this->getBaseUrl() . '/test/folder/add/?parentId=' . $parentId . '&isSuite=' . $isSuite . '" class="ctmButton">' .
                    $newFolderText .
                    '</a>'
                    );
            $this->printHtml('</center></td>');
        }

        $this->printHtml('</table>');
        $this->printHtml('</div>');

    }

}
