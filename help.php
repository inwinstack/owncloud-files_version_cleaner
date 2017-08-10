<?php
namespace OCA\Files_Version_Cleaner;
use OCP\Defaults;
use OCP\User;
use OCP\Util as CoreUtil;
use OCP\Template;
// Check if we are a user
User::checkLoggedIn();
$appManager = \OC::$server->getAppManager();
$config = \OC::$server->getConfig();
$defaults = new Defaults();
$tmpl = new Template('files_version_cleaner', 'help', '');
$tmpl->printPage();
