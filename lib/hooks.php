<?php

namespace OCA\Files_Version_Cleaner;

use OCA\Files_Version_Cleaner\DatabaseVersionCleanerHandler;

/**
 * Class Hooks
 * @author Dauba
 */
class Hooks
{
    /**
     * App name
     *
     * @var string
     */
    private $appName;

    /**
     * System config
     *
     * @var \OC\SystemConfig
     */
    private $config;

    /**
     * user session
     *
     * @var \OC\User
     */
    private $userSession;

    /**
     * User folder
     *
     * @var \OCP\Files\Folder
     */
    private $userFolder;

    /**
     * files version cleaner
     *
     * @var \OCA\Files_Version_Cleaner
     */
    private $filesVersionCleaner;

    /**
     * user root view
     *
     * @var \OC\Files\View
     */
    private $view;

    /**
     * @param mixed $userFolder
     */
    public function __construct($appName, $userSession, $userFolder, $config, $filesVersionCleaner, $userRootView)
    {
        $this->uid = \OC_User::getUser();
        $this->userFolder = $userFolder;
        $this->filesVersionCleaner = $filesVersionCleaner;
        $this->userSession = $userSession;
        $this->config = $config;
        $this->appName = $appName;
        $this->view = $userRootView;
    }
    
    /**
     * register
     * @return void
     * @author Dauba
     **/
    public function register()
    {
        $postWriteCallback = array($this, "postWriteHook");
        $preDeleteCallback = array($this, "preDeleteHook");

        $this->userFolder->listen("\OC\Files", "postWrite", $postWriteCallback);
        $this->userFolder->listen("\OC\Files", "preDelete", $preDeleteCallback);

        \OCP\Util::connectHook("OC_Filesystem", "post_rename", "\OCA\Files_Version_Cleaner\Hooks", "renameHook");
    }

    /**
     * Hook for post rename, update file version cleaner data.
     *
     * @return void
     */
    public static function renameHook($params)
    {
        $oldDirName = dirname($params["oldpath"]);
        $newDirName = dirname($params["newpath"]);
        $view = new \OC\Files\View("/" . \OC_User::getUser() . "/files");

        if($view->is_dir($params["newpath"])) {
            DatabaseVersionCleanerHandler::updateData($params["oldpath"], $params["newpath"]);
            if(DatabaseVersionCleanerHandler::read($newDirName)) {
                $application = new \OCA\Files_Version_Cleaner\Appinfo\Application();
                $filesVersionsCleaner = $application->getContainer()->query("FilesVersionCleaner");
                $folders = $filesVersionsCleaner->findAllSubFolder($params["newpath"]);
                $folders[] = $params["newpath"];

                DatabaseVersionCleanerHandler::write($folders);
            }
        }
        else {
            if (DatabaseVersionCleanerHandler::read($oldDirName)) {
                if (!DatabaseVersionCleanerHandler::read($newDirName)) {
                    \OCA\Files_Versions\Storage::markDeletedFile($params["oldpath"]);
                    \OCA\Files_Versions\Storage::delete($params["oldpath"]);
                }
            }
        }
    }

    /**
     * Hook for post delete, update file version cleaner data and delete versions of file or folder.
     *
     * @return void
     */
    public function preDeleteHook($node)
    {
        $view = new \OC\Files\View("/" . \OC_User::getUser() . "/files");
        $relativePath = $view->getRelativePath($node->getPath());
        if($node->getType() === "dir") {
            DatabaseVersionCleanerHandler::deleteData($relativePath);
            $this->filesVersionCleaner->cleanAllVersions($relativePath);
        }
        else {
            $dirName = $view->getRelativePath($node->getParent()->getpath());
            if (DatabaseVersionCleanerHandler::read($dirName)) {
                $versions = \OCA\Files_Versions\Storage::getVersions($this->uid, $relativePath);
                if (!empty($versions)) {
                    foreach ($versions as $v) {
                        $this->filesVersionCleaner->delete($v["path"], $v["version"]);
                    }
                }
            }
        }
    }

    /**
     * Hook for post write. Delete redundant versions.
     *
     * @return void
     */
    public function postWriteHook($fileNode)
    {
        if($fileNode->getType() === "dir") {
            $folder = array();
            $view = new \OC\Files\View("/" . \OC_User::getUser() . "/files");
            $dirName = $view->getRelativePath($fileNode->getParent()->getpath());
            if (DatabaseVersionCleanerHandler::read($dirName)) {
                $folder[] = $view->getRelativePath($fileNode->getPath());
                DatabaseVersionCleanerHandler::write($folder);
            }
        }

        $relativePath = $this->view->getRelativePath($fileNode->getFileInfo()->getPath());
        list($uid, $fileName) = \OCA\Files_Versions\Storage::getUidAndFilename($relativePath);
        $versions[] = \OCA\Files_Versions\Storage::getVersions($uid, $fileName);
        $this->filesVersionCleaner->deleteVersion($versions, $uid);
    }
}
