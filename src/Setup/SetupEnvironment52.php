<?php
/* Copyright (c) 2016, 2017 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

/**
 * header include for ilias
 *
 * @author	Stefan Hecken <stefan.hecken@concepts-and-training.de>
 * @author Richard Klees <richard.klees@concepts-and-training.de>
 */

namespace CaT\Ilse\Setup;

class SetupEnvironment52
{
	/**
	 * @var string
	 */
	protected $http_path;

	/**
	 * @var string
	 */
	protected $absolute_path;

	/**
	 * @var string
	 */
	protected $data_path;

	/**
	 * @var string
	 */
	protected $client_id;

	public function __construct($http_path,
								$absolute_path,
								$data_path,
								$client_id
								)
	{
		$this->http_path = $http_path;
		$this->absolute_path = $absolute_path;
		$this->data_path = $data_path;
		$this->client_id = $client_id;
	}

	public function changeDirToILIASRoot() {
		chdir($this->absolute_path);
	}

	public function initPHPErrorReporting() {
		// remove notices from error reporting
		if (version_compare(PHP_VERSION, '5.3.0', '>='))
		{
			error_reporting((ini_get("error_reporting") & ~E_NOTICE) & ~E_DEPRECATED & ~E_STRICT);
		}
		else
		{
			error_reporting(ini_get('error_reporting') & ~E_NOTICE & ~E_STRICT);
		}
	}

	public function defineConstantsAndSuperglobals() {
		define("DEBUG",false);

		define ("ILIAS_HTTP_PATH", $this->http_path);
		define ("ILIAS_ABSOLUTE_PATH", $this->absolute_path);
		define ("ILIAS_DATA_DIR", $this->data_path);
		define ("ILIAS_WEB_DIR", "data");
		define ("CLIENT_DATA_DIR",ILIAS_DATA_DIR."/".$this->client_id);
		define ("CLIENT_WEB_DIR",ILIAS_ABSOLUTE_PATH."/".ILIAS_WEB_DIR."/".$this->client_id);
		define ("CLIENT_ID", $this->client_id);
		define('IL_PHPUNIT_TEST', true);
		define ("TPLPATH","./templates/blueshadow");
		define('IL_PHPUNIT_TEST', true);

		$this->lang 				= "de";
		$_COOKIE['ilClientId'] 		= $this->client_id;
		$_SESSION['lang'] 			= $this->lang;
	}

	public function includeSource() {
		include_once $this->absolute_path.'/libs/composer/vendor/autoload.php';
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
		require_once "./Services/Database/classes/class.ilDBUpdate.php";
		require_once "./setup/classes/class.ilSetup.php";
		require_once "./Services/User/classes/class.ilUserPasswordEncoderFactory.php";
		require_once "./Services/Password/exceptions/class.ilPasswordException.php";
		require_once "./Services/Logging/classes/public/class.ilLoggerFactory.php";
		require_once "./Services/GlobalCache/classes/class.ilGlobalCache.php";

		// include error_handling
		require_once "./Services/Init/classes/class.ilErrorHandling.php";
	}

	public function initErrorHandling()
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

	public function initLog()
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

	public function initTemplate()
	{
		$tpl = new ilTemplate("tpl.main.html", true, true, "setup");
		$GLOBALS['ilTemplate'] = $tpl;
	}

	public function initStructureReader()
	{
		$ilCtrlStructureReader = new \ilCtrlStructureReader();
		$ilCtrlStructureReader->setErrorObject($this->ilErr);
		$GLOBALS['ilCtrlStructureReader'] = $ilCtrlStructureReader;
	}

	public function initBenchmark()
	{
		require_once "./Services/Utilities/classes/class.ilBenchmark.php";
		$ilBench = new \ilBenchmark();
		$GLOBALS['ilBench'] = $ilBench;

		include_once("./Services/Database/classes/class.ilDBAnalyzer.php");
		include_once("./Services/Database/classes/class.ilMySQLAbstraction.php");
		include_once("./Services/Database/classes/class.ilDBGenerator.php");
	}

	public function initIni()
	{
		global $DIC;
		include_once './Services/Init/classes/class.ilIniFile.php';
		$ini = new \ilIniFile(ILIAS_ABSOLUTE_PATH.'/ilias.ini.php');
		$ini->read();
		$GLOBALS['ini'] = $ini;
		$DIC["ini"] = function($c) { return $GLOBALS["ini"]; };
	}
}
