<?php

namespace OCA\Files_Version_Cleaner\AppInfo;

use OCP\AppFramework\App;

/**
 * Class Application
 * @author Dauba
 */
class Application Extends App
{
    /**
     * @param mixed array $urlParams = array()
     */
    public function __construct(array $urlParams = array()){
        parent::__construct("files_version_cleaner", $urlParams);

        $container = $this->getContainer();

        $container->registerService("FilesVersionCleanerController", function($c) {
            return new \OCA\Files_Version_Cleaner\Controller\FilesVersionCleaner(
                $c->getAppName(),
                \OC::$server->getConfig(),
                $c->query("FilesVersionCleaner")
            );
        });

        $container->registerService("FilesVersionCleaner", function($c){
            return new \OCA\Files_Version_Cleaner\FilesVersionCleaner(
                $c->getAppName(),
                $c->query("UserRootView")
            );
        });

        $container->registerService("Hooks", function($c){
            return new \OCA\Files_Version_Cleaner\Hooks(
                $c->getAppName(),
                $c->query("ServerContainer")->getUserSession(),
                $c->query("ServerContainer")->getRootFolder(),
                $c->query("ServerContainer")->getSystemConfig(),
                $c->query("FilesVersionCleaner")
            );
        });

        $container->registerService("UserRootView", function($c){
            return new \OC\Files\View("/" . \OC_User::getUser() . "/files");
        });
    }
    
}
