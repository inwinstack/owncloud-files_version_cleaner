<?php
if(!\OCP\Config::getSystemValue('files_version_cleaner_default_version_number')) {
    \OCP\Config::setSystemValue('files_version_cleaner_default_version_number', 2);
}
if(!\OCP\Config::getSystemValue('files_version_cleaner_max_version_number')) {
    \OCP\Config::setSystemValue('files_version_cleaner_max_version_number', 10);
}

if(!\OCP\Config::getSystemValue('files_version_cleaner_default_historic_version_number')) {
    \OCP\Config::setSystemValue('files_version_cleaner_default_historic_version_number', 2);
}
if(!\OCP\Config::getSystemValue('files_version_cleaner_max_historic_version_number')) {
    \OCP\Config::setSystemValue('files_version_cleaner_max_historic_version_number', 10);
}

if(!\OCP\Config::getSystemValue('files_version_cleaner_default_interval')) {
    \OCP\Config::setSystemValue('files_version_cleaner_default_interval', 1);
}
