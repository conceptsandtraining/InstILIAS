<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

/**
* header include for ilias
*
* @author	Stefan Hecken <stefan.hecken@concepts-and-training.de>
*/

namespace CaT\Ilse\Executor;

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
require_once("./Services/Component/classes/class.ilCachedComponentData.php");
require_once("Services/Database/classes/class.ilDBUpdate.php");
require_once("setup/classes/class.ilSetup.php");
require_once 'Services/User/classes/class.ilUserPasswordEncoderFactory.php';
require_once("Services/Password/exceptions/class.ilPasswordException.php");
require_once("Services/Logging/classes/public/class.ilLoggerFactory.php");

// include error_handling
require_once "./Services/Init/classes/class.ilErrorHandling.php";

class IlseSetupHeader
{
	public function __construct($http_path,
								$absolute_path,
								$data_path,
								$web_dir,
								$client_id
								)
	{
		define ("ILIAS_HTTP_PATH", $http_path);
		define ("ILIAS_ABSOLUTE_PATH", $absolute_path);
		define ("ILIAS_DATA_DIR", $data_path);
		define ("ILIAS_WEB_DIR", $web_dir);
		define ("CLIENT_DATA_DIR",ILIAS_DATA_DIR."/".$client_id);
		define ("CLIENT_WEB_DIR",ILIAS_ABSOLUTE_PATH."/".ILIAS_WEB_DIR."/".$client_id);
		define ("CLIENT_ID", $client_id);
		define('IL_PHPUNIT_TEST', true);
		define ("TPLPATH","./templates/blueshadow");
		define('IL_PHPUNIT_TEST', true);

		$this->lang 				= "de";
		$_COOKIE['ilClientId'] 		= $client_id;
		$_SESSION['lang'] 			= $this->lang;
	}

	public function init()
	{
		$this->setErrorHandling();
		$this->initLog();
		$this->initStructureReader();
		$this->initBenchmark();
		$this->initIni();
	}

	protected function setErrorHandling()
	{
		$this->ilErr = new \ilErrorHandling();
		$this->ilErr->setErrorHandling(PEAR_ERROR_CALLBACK, array($this->ilErr,'errorHandler'));
	}

	public function initLanguage()
	{
		$lng = new \ilLanguage($this->lang);
		$GLOBALS['lng'] = $lng;
		return $lng;
	}

	protected function initLog()
	{
		global $DIC;
		include_once './Services/Logging/classes/class.ilLoggingSetupSettings.php';
		$logging_settings = new \ilLoggingSetupSettings();
		$logging_settings->init();

		include_once './Services/Logging/classes/public/class.ilLoggerFactory.php';

		$log = \ilLoggerFactory::newInstance($logging_settings)->getComponentLogger('setup');
		$ilLog = $log;
		$GLOBALS['ilLog'] = $log;
		$DIC["ilLog"] = function($c) { return $GLOBALS["ilLog"]; };
	}

	protected function intiTemplate()
	{
		$tpl = new ilTemplate("tpl.main.html", true, true, "setup");
		$GLOBALS['ilTemplate'] = $tpl;
	}

	protected function initStructureReader()
	{
		$ilCtrlStructureReader = new \ilCtrlStructureReader();
		$ilCtrlStructureReader->setErrorObject($this->ilErr);
		$GLOBALS['ilCtrlStructureReader'] = $ilCtrlStructureReader;
	}

	protected function initBenchmark()
	{
		require_once "./Services/Utilities/classes/class.ilBenchmark.php";
		$ilBench = new \ilBenchmark();
		$GLOBALS['ilBench'] = $ilBench;

		include_once("./Services/Database/classes/class.ilDBAnalyzer.php");
		include_once("./Services/Database/classes/class.ilMySQLAbstraction.php");
		include_once("./Services/Database/classes/class.ilDBGenerator.php");
	}

	protected function initIni()
	{
		global $DIC;
		include_once './Services/Init/classes/class.ilIniFile.php';
		$ini = new \ilIniFile(ILIAS_ABSOLUTE_PATH.'/ilias.ini.php');
		$ini->read();
		$GLOBALS['ini'] = $ini;
		$DIC["ini"] = function($c) { return $GLOBALS["ini"]; };
	}
}