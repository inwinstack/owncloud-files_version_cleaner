<?php

namespace OCA\Files_Version_Cleaner;

/**
 * Class DatabaseVersionCleanerHandler
 * @author Dauba
 */
class DatabaseVersionCleanerHandler
{
    const WRITE_PATH_QUERY = "INSERT INTO `files_version_cleaner` (userid, path) VALUES ";
    const DELETE_PATH_QUERY = "DELETE FROM `files_version_cleaner` WHERE userid = ? AND path LIKE ?";
    const UPDATE_PATH_QUERY = "UPDATE `files_version_cleaner` SET path = REPLACE (path, ?, ?) WHERE userid= ? AND path LIKE ?";
    const READ_PATH_QUERY = "SELECT userid, path FROM `files_version_cleaner` WHERE userid=? AND path=?";

    /**
     * write database
     *
     * @param array folder absolute path 
     * @return void
     */
    public static function write($paths)
    {
        $writeQuery = self::WRITE_PATH_QUERY;
        $uid = \OC_User::getUser();
        foreach ($paths as $path) {
            $writeQuery = $writeQuery . "('$uid','$path'),";
        }
        $writeQuery = substr($writeQuery, 0, -1);
        $connection = \OC::$server->getDatabaseConnection();
        $prepare = $connection->prepare($writeQuery);
        $prepare->execute();
    }

    /**
     * delete data form files_versions_clener
     *
     * @param String folder absolute path
     * @return bool
     */
    public static function deleteData($path) {
        $uid = \OC_User::getUser();
        $condtion = array($uid, $path.'%');
        $connection = \OC::$server->getDatabaseConnection();
        $prepare = $connection->prepare(self::DELETE_PATH_QUERY);
        if(!$prepare->execute($condtion)) {
            return false;
        }
        
        return true;
    }

    /**
     * update data form files_versions_clener
     *
     * @param String folder absolute original path
     * @param String folder absolute replacement path
     * @return bool
     */
    public static function updateData($original, $replacement) {
        $uid = \OC_User::getUser();
        $sqlData = array($original, $replacement, $uid, $original.'%');
        $connection = \OC::$server->getDatabaseConnection();
        $prepare = $connection->prepare(self::UPDATE_PATH_QUERY);

        if(!$prepare->execute($sqlData)) {
            return false;
        }
        
        return true;
    }

    /**
     * read data from files_version_cleaner
     *
     * @param string folder name
     * @return array
     */
    public static function read($folderName)
    {
        $uid = \OC_User::getUser();
        $connection = \OC::$server->getDatabaseConnection();
        $prepare = $connection->prepare(self::READ_PATH_QUERY);
        $params = array($uid, $folderName);

        if(!$prepare->execute($params)) {
            return false;
        }
        
        $result = $prepare->fetch();

        return $result;
    }
}
