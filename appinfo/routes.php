<?php
/**
 * ownCloud - files_version_cleaner
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author DaubaKao <dauba.k@inwinstack.com>
 * @copyright DaubaKao 2016
 */

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Files_Version_Cleaner\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
$this->create('files_version_cleaner_help', 'help.php')
->actionInclude('files_version_cleaner/help.php');
return [
    'routes' => [
        ['name' => 'FilesVersionCleaner#setUserVersionNumber', 'url' => '/set_number', 'verb' => 'POST'],
        ['name' => 'FilesVersionCleaner#setUserInterval', 'url' => '/setInterval', 'verb' => 'POST'],
        ['name' => 'FilesVersionCleaner#deleteVersion', 'url' => '/deleteVersion', 'verb' => 'GET'],
        ['name' => 'FilesVersionCleaner#setUserVersionFolder', 'url' => '/folderStatus', 'verb' => 'POST'],
        ['name' => 'FilesVersionCleaner#getUserVersionFolder', 'url' => '/folderStatus', 'verb' => 'GET'],
        ['name' => 'FilesVersionCleaner#getVersionsNumber', 'url' => '/getVersionNumber', 'verb' => 'GET'],
    ]
];
