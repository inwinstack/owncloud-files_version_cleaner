<?php

namespace OCA\Files_Version_Cleaner;

/**
 * Class Hooks
 * @author Dauba
 */
class Hooks
{
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
    public function __construct($userFolder, $filesVersionCleaner)
    {
        $this->uid = \OC_User::getUser();
        $this->userFolder = $userFolder;
        $this->filesVersionCleaner = $filesVersionCleaner;
    }
    
    /**
     * register
     * @return void
     * @author Dauba
     **/
    public function register()
    {
        $callback = array($this, "deleteVersion");
        $this->userFolder->listen("\OC\Files", "postWrite", $callback);
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
}
