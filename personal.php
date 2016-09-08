<?php
$config = \OC::$server->getConfig();
$systemConfig = \OC::$server->getSystemConfig();

$userVersionNum = $config->getUserValue(\OC_User::getUser(), "files_version_cleaner", "versionNumber", $systemConfig->getValue("files_version_cleaner_default_version_number", 5));
$maxVersionNum = $systemConfig->getValue("files_version_cleaner_max_version_number", 10);

$userHistoricVersionNum = $config->getUserValue(\OC_User::getUser(), "files_version_cleaner", "historicVersionNumber", $systemConfig->getValue("files_version_cleaner_default_version_number", 5));
$maxHistoricVersionNum = $systemConfig->getValue("files_version_cleaner_max_historic_version_number", 10);

$interval = $config->getUserValue(\OC_User::getUser(), "files_version_cleaner", "interval", $systemConfig->getValue("files_version_cleaner_default_interval", 24));

$tmpl = new OCP\Template("files_version_cleaner", "personal");
$tmpl->assign("userVersionNum", $userVersionNum);
$tmpl->assign("maxVersionNum", $maxVersionNum);
$tmpl->assign("userHistoricVersionNum", $userHistoricVersionNum);
$tmpl->assign("maxHistoricVersionNum", $maxHistoricVersionNum);
$tmpl->assign("interval", $interval);

return $tmpl->fetchPage();
