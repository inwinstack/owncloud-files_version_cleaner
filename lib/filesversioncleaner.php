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
     * view of files_versions
     *
     * @var OC\Files\View
     */
    private $filesVersionsView;

    /**
     * @param OC\Files\View $view
     * @param path $root
     */
    public function __construct($appName, $filesView)
    {
        $this->appName = $appName;
        $this->filesView = $filesView;
        $this->uid = \OC_User::getUser();
        $this->filesVersionsView = new \OC\Files\View('/' . $this->uid . '/files_versions');
        $this->nowDate = date("z") + 1;
    }

    public function deleteVersions($root, $type = NULL)
    {
        $files = $this->filesView->getDirectoryContent($root, NULL);
        $func = $type === "historic" ? array($this, "deleteHistoricVersion") : array($this, "deleteVersion");
        $func = array($this, "deleteVersion");
        
        foreach ($files as $file) {
            $relativePath = $this->filesView->getRelativePath($file->getPath());

            if($file->getType() === "dir") {
                self::deleteVersions($relativePath, $type);
            }
            else {
                $versions[] = \OCA\Files_Versions\Storage::getVersions($this->uid, $relativePath);
            }
        }

        call_user_func($func, $versions);
    }
    
    public function deleteVersion($versions)
    {
        $userMaxVersionNum = (int)\OCP\Config::getUserValue($this->uid, $this->appName, "versionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_version_number'));
        $userMaxHistoricVersionNum = (int)\OCP\Config::getUserValue($this->uid, $this->appName, "historicVersionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_historic_version_number'));
        $interval = (int)\OCP\Config::getUserValue($this->uid, $this->appName, "interval", \OCP\Config::getSystemValue('files_version_cleaner_default_interval'));

        $toDelete = array();

        foreach ($versions as $version) {
            $toPreserve = array();
            $version = array_values($version);
            for ($index1 = $userMaxVersionNum, $index2 = $userMaxVersionNum + 1; array_key_exists($index2, $version) && count($toPreserve) < $userMaxHistoricVersionNum;) {
                if ((int)$version[$index1]["version"] - (int)$version[$index2]["version"] < 60*60*$interval) {
                    $toDelete[] = $version[$index2];
                }
                else{
                    $toPreserve[] = $version[$index1];
                    $index1 = $index2;
                }
                $index2++;
            }

            if (count($toPreserve) < $userMaxHistoricVersionNum) {
                $index1++;
            }

            for ($i = $index1; array_key_exists($i, $version); $i++) {
                $toDelete[] = $version[$i];
            }
        }

        if (!empty($toDelete)) {
            foreach ($toDelete as $v) {
                self::delete($v["path"], $v["version"]);
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
