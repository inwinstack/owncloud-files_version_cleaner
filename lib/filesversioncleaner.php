<?php

namespace OCA\Files_Version_Cleaner;

/**
 * Class FilesVersionCleaner
 * @author Dauba
 */
class FilesVersionCleaner
{

    /**
     * @var OC\Files\View
     */
    private $filesView;

    /**
     * view of files_versions
     *
     * @var OC\Files\View
     */
    private $filesVersionsView;

    /**
     * @param OC\Files\View $view
     * @param path $root
     */
    public function __construct($filesView)
    {
        $this->filesView = $filesView;
        $this->uid = \OC_User::getUser();
        $this->filesVersionsView = new \OC\Files\View('/' . $this->uid . '/files_versions');
    }

    public function deleteVersions($root)
    {
        $files = $this->filesView->getDirectoryContent($root, NULL);
        
        foreach ($files as $file) {
            $relativePath = $this->filesView->getRelativePath($file->getPath());

            if($file->getType() === "dir") {
                self::deleteVersions($relativePath);
            }
            else {
                $versions[] = \OCA\Files_Versions\Storage::getVersions($this->uid, $relativePath);
            }
        }

        $this->deleteVersion($versions);
    }
    
    public function deleteVersion($versions)
    {
        $userMaxVersionNum = \OCP\Config::getUserValue($this->uid, "files_version_cleaner", "versionNumber");

        foreach ($versions as $version) {
            if (count($version) > $userMaxVersionNum) {
                $toDelete = array_slice($version, $userMaxVersionNum);

                if (!empty($toDelete)) {
                    foreach ($toDelete as $v) {
                        self::delete($v["path"], $v["version"]);
                    }
                }
            }
        }
    }
    
    public function delete($path, $revision)
    {
        $view = new \OC\Files\View('/' . \OC_User::getUser() . '/files_versions');
        $view->unlink($path . ".v" . $revision);
        list($storage, $internalPath) = $view->resolvePath($path);
        $cache = $storage->getCache($internalPath);
        $cache->remove($internalPath);
    }
}
