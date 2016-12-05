<?php

namespace OCA\Files_Version_Cleaner;

/**
 * Class FilesVersionCleaner
 * @author Dauba
 */
class FilesVersionCleaner
{
    /**
     * App name
     *
     * @var string
     */
    private $appName;

    /**
     * @var OC\Files\View
     */
    private $filesView;

    /**
     * @param OC\Files\View $view
     * @param path $root
     */
    public function __construct($appName, $filesView)
    {
        $this->appName = $appName;
        $this->filesView = $filesView;
        $this->uid = \OC_User::getUser();
        $this->nowDate = date("z") + 1;
    }

    /**
     * find all sub folder
     *
     * @return array folders
     */
    public function findAllSubFolder($root)
    {
        $folders = array();
        $files = $this->filesView->getDirectoryContent($root, NULL);
        foreach ($files as $file) {
            $relativePath = $this->filesView->getRelativePath($file->getPath());
            if ($file->getType() === "dir" && !$file->isShared()) {
                $folders[] = $relativePath;
                $folders = array_merge($folders, self::findAllSubFolder($relativePath));
            }
        }

        return $folders;
    }

    public function deleteVersions($root, $type = NULL)
    {
        $files = $this->filesView->getDirectoryContent($root, NULL);
        $func = array($this, "deleteVersion");
        $versions = array();
        
        foreach ($files as $file) {
            $relativePath = $this->filesView->getRelativePath($file->getPath());

            if($file->getType() === "dir") {
                self::deleteVersions($relativePath, $type);
            }
            else {
                $versions[] = \OCA\Files_Versions\Storage::getVersions($this->uid, $relativePath);
            }
        }

        if ($versions) {
            call_user_func($func, $versions);
        }
    }
    
    public function deleteVersion($versions, $uid = '')
    {
        $uid = !$uid ? $this->uid : $uid;
        $userMaxVersionNum = (int)\OCP\Config::getUserValue($uid, $this->appName, "versionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_version_number'));
        $userMaxHistoricVersionNum = (int)\OCP\Config::getUserValue($uid, $this->appName, "historicVersionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_historic_version_number'));
        $interval = (int)\OCP\Config::getUserValue($uid, $this->appName, "interval", \OCP\Config::getSystemValue('files_version_cleaner_default_interval'));

        $toDelete = array();

        foreach ($versions as $version) {
            $toPreserve = 0;
            $version = array_values($version);
            for ($index1 = $userMaxVersionNum, $index2 = $userMaxVersionNum + 1; $index1 < count($version); $index2++) {
                if (date("z", (int)$version[$index1]["version"]) == date("z", (int)$version[$index2]["version"])) {
                    $toDelete[] = $version[$index2];
                }
                else if($toPreserve < $userMaxHistoricVersionNum) {
                    $toPreserve++;
                    $index1 = $index2;
                }
                else {
                    if (array_key_exists($index1, $version)) {
                        $toDelete[] = $version[$index1];
                    }
                    $index1++;
                }
            }
        }

        if (!empty($toDelete)) {
            foreach ($toDelete as $v) {
                self::delete($v["path"], $v["version"], $uid);
            }
        }
    }

    /**
     * delete all versions when cancel version control on folder
     *
     * @return void
     */
    public function cleanAllVersions($root) {
        $files = $this->filesView->getDirectoryContent($root, NULL);
        $view = new \OC\Files\View('/' . $this->uid . '/files_versions');

        foreach ($files as $file) {
            $relativePath = $this->filesView->getRelativePath($file->getPath());

            if(!$file->isShared()) {
                if($file->getType() === "dir") {
                    self::cleanAllVersions($relativePath);
                }
                else {
                    $versions = \OCA\Files_Versions\Storage::getVersions($this->uid, $relativePath);
                    if (!empty($versions)) {
                        foreach ($versions as $v) {
                            \OC_Hook::emit('\OCP\Versions', 'preDelete', array('path' => $relativePath . $v['version']));
                            self::delete($relativePath, $v['version']);
                            \OC_Hook::emit('\OCP\Versions', 'delete', array('path' => $relativePath . $v['version']));
                        }
                    }
                }
            }
        }
    }
    
    public function delete($path, $revision, $uid = '')
    {
        $uid = !$uid ? $this->uid : $uid;
        $view = new \OC\Files\View('/' . $uid . '/files_versions');
        $view->unlink($path . ".v" . $revision);
        list($storage, $internalPath) = $view->resolvePath($path);
        $cache = $storage->getCache($internalPath);
        $cache->remove($internalPath);
    }

    /**
     * get user version number
     *
     * @return int version number
     **/
    public function getVersionsNumber($uid = '')
    {
        $uid = !$uid ? $this->uid : $uid;
        $versionNumber = (int)\OCP\Config::getUserValue($uid, $this->appName, "versionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_version_number'));

        return $versionNumber;
    }
}
