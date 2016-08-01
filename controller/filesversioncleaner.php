<?php

namespace OCA\Files_Version_Cleaner\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;

/**
 * Class FilesVersionCleaner
 * @author Dauba
 */
class FilesVersionCleaner extends Controller
{
    /**
     * OC\AllConfig
     *
     * @var object
     */
    private $config;

    /**
     * files version cleaner
     *
     * @var \OCA\Files_Version_Cleaner
     */
    private $filesVersionCleaner;

    /**
     * @param mixed 
     */
    public function __construct($config, $filesVersionCleaner)
    {
        $this->config = $config;
        $this->filesVersionCleaner = $filesVersionCleaner;
    }

    /**
     * setUserVersionNumber
     * @return json response
     * @author Dauba
     **/
    public function setUserVersionNumber($versionNumber, $filesVersionCleaner) {
        $result = array();
        $uid = \OC_User::getUser();

        $this->config->setUserValue($uid, "files_version_cleaner", "versionNumber", $versionNumber);

        $result["success"] = $this->config->getUserValue($uid, "files_version_cleaner", "versionNumber") ? true : false;

        if ($result["success"]) {
            $this->filesVersionCleaner->deleteVersions("/");
        }

        return new JSONResponse($result);
    }
}
