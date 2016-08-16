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

    /**
     * Delete historic version
     *
     * @return void
     */
    public function deleteHistoricVersion($versions)
    {
        $userMaxHistoricVersionNum = \OCP\Config::getUserValue($this->uid, $this->appName, "historicVersionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_historic_version_number'));

        for ($i = 0; $i < count($versions); $i++) {
            $j = 0;
            foreach ($versions[$i] as $version) {
                if (date("z", (int)$version["version"]) + 1 - $this->nowDate != 0) {
                    break;
                }
                $j++;
            }
            $versions[$i] = array_values(array_slice($versions[$i], $j));
        }

        foreach ($versions as $version) {
            for ($i = count($version) - 1; $i > 0; $i--) {
                if (date("z", (int)$version[$i]["version"]) == date("z", (int)$version[$i-1]["version"])) {
                    $toDelete[] = $version[$i];
                    array_splice($version[$i], $i, 1);
                }
            }

            if (!empty($toDelete)) {
                foreach ($toDelete as $v) {
                    self::delete($v["path"], $v["version"]);
                }
            }

            if (count($version) > $userMaxHistoricVersionNum) {
                $toDelete1 = array_slice($version, $userMaxHistoricVersionNum);
                foreach ($toDelete1 as $v) {
                    self::delete($v["path"], $v["version"]);
                }
            }
        }
    }
    
    
    public function deleteVersion($versions)
    {
        $userMaxVersionNum = \OCP\Config::getUserValue($this->uid, $this->appName, "versionNumber", \OCP\Config::getSystemValue('files_version_cleaner_default_version_number'));

        for ($i = 0; $i < count($versions); $i++) {
            $j = 0;
            foreach ($versions[$i] as $version) {
                if (date("z", (int)$version["version"]) + 1 - $this->nowDate != 0) {
                    break;
                }
                $j++;
            }
            $versions[$i] = array_slice($versions[$i], 0, $j);
        }

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
