<?php
/**
 * ownCloud - files_version_cleaner
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author DaubaKao <dauba.k@inwinstack.com>
 * @copyright DaubaKao 2016
 */

namespace OCA\Files_Version_Cleaner\AppInfo;

use OCP\AppFramework\App;
use OCA\Files_Versions\Storage;

$app = new Application();

$app->getContainer()->query("Hooks")->register();

\OCP\App::registerPersonal("files_version_cleaner", "personal");

