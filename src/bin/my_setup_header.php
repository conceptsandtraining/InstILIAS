<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

/**
* header include for ilias
*
* @author	Stefan Hecken <stefan.hecken@concepts-and-training.de>
*/

// remove notices from error reporting
if (version_compare(PHP_VERSION, '5.3.0', '>='))
{
	error_reporting((ini_get("error_reporting") & ~E_NOTICE) & ~E_DEPRECATED & ~E_STRICT);
}
else
{
	error_reporting(ini_get('error_reporting') & ~E_NOTICE & ~E_STRICT);
}

define("DEBUG",false);
// wrapper for php 4.3.2 & higher

require_once "./setup/classes/class.ilTemplate.php";	// modified class. needs to be merged with base template class 
require_once "./setup/classes/class.ilLanguage.php";	// modified class. needs to be merged with base language class 
require_once "./Services/Logging/classes/class.ilLog.php";
require_once "./Services/Authentication/classes/class.ilSession.php";
require_once "./Services/Utilities/classes/class.ilUtil.php";
require_once "./Services/Init/classes/class.ilIniFile.php";
require_once "./Services/Database/classes/MDB2/class.ilDB.php";
require_once "./setup/classes/class.ilSetupGUI.php";
require_once "./setup/classes/class.Session.php";
require_once "./setup/classes/class.ilClientList.php";
require_once "./setup/classes/class.ilClient.php";
require_once "./Services/FileSystem/classes/class.ilFile.php";
require_once "./setup/classes/class.ilCtrlStructureReader.php";
require_once "./Services/Xml/classes/class.ilSaxParser.php";
require_once "./include/inc.ilias_version.php";
require_once("Services/Database/classes/class.ilDBUpdate.php");
require_once("setup/classes/class.ilSetup.php");
require_once 'Services/User/classes/class.ilUserPasswordEncoderFactory.php';
require_once("Services/Password/exceptions/class.ilPasswordException.php");
require_once("Services/Logging/classes/public/class.ilLoggerFactory.php");

// include error_handling
require_once "./Services/Init/classes/class.ilErrorHandling.php";

$ilErr = new ilErrorHandling();
$ilErr->setErrorHandling(PEAR_ERROR_CALLBACK,array($ilErr,'errorHandler'));

define ("ILIAS_HTTP_PATH", $http_path);
define ("ILIAS_ABSOLUTE_PATH", $absolute_path);
define ("ILIAS_DATA_DIR", $data_path);
define ("ILIAS_WEB_DIR", $web_dir);
define ("CLIENT_DATA_DIR",ILIAS_DATA_DIR."/".$client_id);
define ("CLIENT_WEB_DIR",ILIAS_ABSOLUTE_PATH."/".ILIAS_WEB_DIR."/".$client_id);
define ("CLIENT_ID", $client_id);
define('IL_PHPUNIT_TEST', true);
$_COOKIE["ilClientId"] = $client_id;

define ("TPLPATH","./templates/blueshadow");

$lang = "de";

$_SESSION["lang"] = $lang;

// init languages
$lng = new ilLanguage($lang);

// init log
include_once './Services/Logging/classes/class.ilLoggingSetupSettings.php';
$logging_settings = new ilLoggingSetupSettings();
$logging_settings->init();

include_once './Services/Logging/classes/public/class.ilLoggerFactory.php';

$log = ilLoggerFactory::newInstance($logging_settings)->getComponentLogger('setup');
$ilLog = $log;
$DIC["ilLog"] = function($c) { return $GLOBALS["ilLog"]; };

// init template
$tpl = new ilTemplate("tpl.main.html", true, true, "setup");

// make instance of structure reader
$ilCtrlStructureReader = new ilCtrlStructureReader();
$ilCtrlStructureReader->setErrorObject($ilErr);

require_once "./Services/Utilities/classes/class.ilBenchmark.php";
$ilBench = new ilBenchmark();
$GLOBALS['ilBench'] = $ilBench;

include_once("./Services/Database/classes/class.ilDBAnalyzer.php");
include_once("./Services/Database/classes/class.ilMySQLAbstraction.php");
include_once("./Services/Database/classes/class.ilDBGenerator.php");

include_once './Services/Init/classes/class.ilIniFile.php';
$ini = new ilIniFile(ILIAS_ABSOLUTE_PATH.'/ilias.ini.php');
$ini->read();
$DIC["ini"] = function($c) { return $GLOBALS["ini"]; };