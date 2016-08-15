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
     * App name
     *
     * @var string
     */
    protected $appName;

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
    public function __construct($appName, $config, $filesVersionCleaner)
    {
        $this->appName = $appName;
        $this->config = $config;
        $this->filesVersionCleaner = $filesVersionCleaner;
    }

    /**
     * setUserVersionNumber
     * @return json response
     * @author Dauba
     **/
    public function setUserVersionNumber($versionNumber, $key) {
        $result = array();
        $uid = \OC_User::getUser();
        $params = array("/");

        if ($key == "historicVersionNumber") {
            $params[] = "historic";
        }

        $oldMaxVersionNum = $this->config->getUserValue($uid, $this->appName, $key);
        $func = array($this->filesVersionCleaner, "deleteVersions");

        $this->config->setUserValue($uid, $this->appName, $key, $versionNumber);

        $result["success"] = $this->config->getUserValue($uid, $this->appName, $key) == $versionNumber ? true : false;

        if ($result["success"] && $versionNumber < $oldMaxVersionNum) {
            call_user_func_array($func, $params);
        }

        return new JSONResponse($result);
    }

    /**
     * Delete specific version
     *
     * @return json response
     */
    public function deleteVersion($file, $revision)
    {
        $result = array();

        $this->filesVersionCleaner->delete($file, $revision);

        $result["success"] = true;
        return new JSONResponse($result);
    }
}
