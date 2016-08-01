<?php
$config = \OC::$server->getConfig();
$systemConfig = \OC::$server->getSystemConfig();

$userVersionNum = $config->getUserValue(\OC_User::getUser(), "files_version_cleaner", "versionNumber", $systemConfig->getValue("files_version_cleaner_default_version_number", 5));
$maxVersionNum = $systemConfig->getValue("files_version_cleaner_max_version", 10);

$tmpl = new OCP\Template("files_version_cleaner", "personal");
$tmpl->assign("userVersionNum", $userVersionNum);
$tmpl->assign("maxVersionNum", $maxVersionNum);

return $tmpl->fetchPage();
