<?php

namespace OCA\Files_Version_Cleaner;

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
     * @param mixed $userFolder
     */
    public function __construct($appName, $userSession, $userFolder, $config, $filesVersionCleaner)
    {
        $this->uid = \OC_User::getUser();
        $this->userFolder = $userFolder;
        $this->filesVersionCleaner = $filesVersionCleaner;
        $this->userSession = $userSession;
        $this->config = $config;
        $this->appName = $appName;
    }
    
    /**
     * register
     * @return void
     * @author Dauba
     **/
    public function register()
    {
        $postWriteCallback = array($this, "deleteVersion");
        $postLoginCallback = array($this, "loginHook");

        $this->userFolder->listen("\OC\Files", "postWrite", $postWriteCallback);
        // $this->userSession->listen("\OC\User", "postLogin", $postLoginCallback);
    }

    /**
     * Hook for post write. Delete redundant versions.
     *
     * @return void
     */
    public function deleteVersion($fileNode)
    {
        $view = new \OC\Files\View("/" . $this->uid . "/files");
        $relativePath = $view->getRelativePath($fileNode->getFileInfo()->getPath());
        $versions[] = \OCA\Files_Versions\Storage::getVersions($this->uid, $relativePath);
        $this->filesVersionCleaner->deleteVersion($versions);
    }

    /**
     * Check user last login time and delete expired versions
     *
     * @return void
     */
    public function loginHook($user)
    {
        $date = date('z') + 1;
        $lastLoginDate = $this->config->getUserValue($this->uid, $this->appName, "lastLoginDate");

        if($lastLoginDate != $date) {
            $this->config->setUserValue($this->uid, $this->appName, "lastLoginDate", $date);
            // $this->filesVersionCleaner;
        }
    }
}
