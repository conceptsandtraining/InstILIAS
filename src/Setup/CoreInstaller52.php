<?php
/* Copyright (c) 2016, 2017 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Setup;

use CaT\Ilse\Config;
use CaT\Ilse\Aux\TaskLogger;

/**
 * implementation of an ilias installer
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 * @author Richard Klees <richard.klees@concepts-and-training.de>
 */

class CoreInstaller52 implements CoreInstaller {
	/**
	 * @var	Config\General
	 */
	protected $config;

	/**
	 * @var	TaskLogger
	 */
	protected $task_logger;

	/**
	 * @var	bool
	 */
	protected $ilias_env_initialized = false;

	/**
	 * @var bool
	 */
	protected $ilias_db_connected = false;

	/**
	 * @var \ilSetup|null
	 */
	protected $ilias_setup = null;

	/**
	 * @var	\ilDBUpdate|null
	 */
	protected $db_update = null;

	const SESSION_EXPIRE_VALUE = 7200;

	public function __construct(Config\General $config, TaskLogger $logger) {
		$this->config= $config;
		$this->task_logger = $logger;
	}

	/**
	 * @return	\ilSetup
	 */
	protected function getILIASSetup() {
		if ($this->ilias_setup === null) {
			$this->initEnvironment();
			$this->ilias_setup = new \ilSetup(true,"admin");
		}
		return $this->ilias_setup;
	}


	/**
	 * @return	\ilDBUpdate
	 */
	protected function getDBUpdate() {
		if ($this->db_update === null) {
			$this->initEnvironment();
			$this->db_update = new \ilDBUpdate($this->getDatabaseHandle());
		}
		return $this->db_update;
	}

	/**
	 * @return	\ilDB
	 */
	protected function getDatabaseHandle() {
		$this->connectDatabase();

		global $ilDB;
		return $ilDB;
	}

	/**
	 * @return \ilLanguage
	 */
	protected function getLanguage() { 
		$this->initEnvironment();
		global $lng;
		$lng->setDbHandler($this->getDatabaseHandle());
		return $lng;
	}

	/**
	 * @return \ilCtrlStructureReader
	 */
	protected function getCtrlStructureReader() { 
		$this->initEnvironment();
		global $ilCtrlStructureReader;
		return $ilCtrlStructureReader;
	}

	/**
	 * @inheritdoc
	 */
	public function initEnvironment() {
		if ($this->ilias_env_initialized) {
			return;
		}
		$env = new SetupEnvironment52
					( $this->config->server()->httpPath()
					, $this->config->server()->absolutePath()
					, $this->config->client->dataDir()
					, $this->config->client->name()
					);

		$this->task_logger->always("Initialize PHP Error Reporting for ILIAS", [$env, "initPHPErrorReporting"]);
		$this->task_logger->always("Defining Constants and Superglobals for ILIAS", [$env, "defineConstantsAndSuperglobals"]);
		$this->task_logger->always("Change to ILIAS Root Dir", [$env, "changeDirToILIASRoot"]);
		$this->task_logger->always("Include required ILIAS Source", [$env, "includeSource"]);
		$this->task_logger->always("Initialize ILIAS Error Handling", [$env, "initErrorHandling"]);
		$this->task_logger->always("Initialize ILIAS Logging", [$env, "initLog"]);
		$this->task_logger->always("Initialize ILIAS Language", [$env, "initLanguage"]);
		$this->task_logger->always("Initialize ILIAS Structure Reader", [$env, "initStructureReader"]);
		$this->task_logger->always("Initialize ILIAS Benchmarking", [$env, "initBenchmark"]);
		$this->task_logger->always("Initialize ILIAS ini", [$env, "initIni"]);

		$this->ilias_env_initialized = true;
	}

	/**
	 * @inheritdoc
	 */
	public function writeILIASIni() {
		$setup = $this->getILIASSetup();
		$ilCtrlStructureReader = $this->getCtrlStructureReader();
		$setup->saveMasterSetup($this->getIliasIniData());
		$ilCtrlStructureReader->setIniFile($setup->ini);
		$setup->ini_ilias_exists = true;
	}

	/**
	 * @inheritdoc
	 */
	public function writeClientIni() {
		$setup = $this->getILIASSetup();
		$setup->ini_client_exists = $this->ilias_setup->newClient($this->config->client()->name());

		$client_ini_data = $this->getClientIniData();
		$client = $setup->getClient();
		$client->setId($client_ini_data["client_id"]);
		$client->setName($client_ini_data["client_id"]);
		$client->setDbHost($client_ini_data["db_host"]);
		$client->setDbName($client_ini_data["db_name"]);
		$client->setDbUser($client_ini_data["db_user"]);
		$client->setDbPort($client_ini_data["db_port"]);
		$client->setDbPass($client_ini_data["db_pass"]);
		$client->setDbType($client_ini_data["db_type"]);
		$client->setDSN();
		$client->ini->setVariable("session", "expire", ($ret["session_expire"] * 60));

		define("SYSTEM_FOLDER_ID", $client->ini->readVariable('system', 'SYSTEM_FOLDER_ID'));

		if(!$setup->saveNewClient()) {
			throw new \RuntimeException($this->ilias_setup->getError());
		}

		$client->status["ini"]["status"] = true;
	}

	/**
	 * @inheritdoc
	 */
	public function installDatabase() {
		$setup = $this->getILIASSetup();
		if((bool)$this->config->database()->createDb()) {
			$setup->createDatabase($this->config->database()->encoding());
		}

		$setup->installDatabase();
	}

	/**
	 * @inheritdoc
	 */
	protected function connectDatabase() {
		if ($this->ilias_db_connected) {
			return;
		}

		$this->task_logger->always("Connecting to Database", [$this->getILIASSetup()->getClient(), "connect"]);

		$this->ilias_db_connected = true;
	}

	/**
	 * @inheritdoc
	 */
	public function applyDatabaseHotfixes() {
		$setup = $this->getILIASSetup();
		$client = $setup->getClient();
		$db_updater = $this->getDBUpdate();

		$ilCtrlStructureReader = $this->getCtrlStructureReader();
		$ilCtrlStructureReader->setIniFile($client->ini);

		$db_updater->applyHotfix();
		$client->status["db"]["status"] = true;
	}

	/**
	 * @inheritdoc
	 */
	public function applyDatabaseUpdates() {
		$setup = $this->getILIASSetup();
		$client = $setup->getClient();
		$db_updater = $this->getDBUpdate();

		$ilCtrlStructureReader = $this->getCtrlStructureReader();
		$ilCtrlStructureReader->setIniFile($client->ini);

		$db_updater->applyUpdate();
	}

	/**
	 * @inheritdoc
	 */
	public function installLanguages() {
		$setup = $this->getILIASSetup();
		$client = $setup->getClient();

		$lng = $this->getLanguage();
		$done = $lng->installLanguages($this->config->language()->available(), array());
		
		if($done !== true) {
			throw new \Exception("Error installing languages");
		}

		$client->setDefaultLanguage($this->config->language()->default());
		$client->status["lang"]["status"] = true;
	}

	/**
	 * @inheritdoc
	 */
	public function setProxySettings() {
		$setup = $this->getILIASSetup();
		$client = $setup->getClient();

		$client->status["proxy"]["status"] = true;
	}

	/**
	 * @inhertidoc
	 */
	public function finishSetup() {
		$setup = $this->getILIASSetup();
		$client = $setup->getClient();

		$client->setSetting("inst_id","0");
		$client->setSetting("nic_enabled","0");
		$client->status["nic"]["status"] = true;

		if (!$this->validateSetup()) {
			throw new \RuntimeException("An unexpected error occured during setup.");
		}


		$setup->ini->setVariable("clients","default",$client->getId());
		$setup->ini->write();

		$client->ini->setVariable("client","access",1);
		$client->ini->write();

		$client->reconnect();
		$client->setSetting("setup_ok",1);
		$client->status["finish"]["status"] = true;
	}

	/**
	 * validatesetup status again
	 * and set access mode of the first client to online
	 */
	protected function validateSetup() {
		// TODO: this suspicously looks as if it only reads the "nic"-status we have set before...
		foreach ($this->ilias_setup->getClient()->status as $key => $val)
		{
			if ($key != "finish" && $key != "access")
			{
				if ($val["status"] != true)
				{
					return false;
				}
			}
		}

		return true;
	}

	protected function getIliasIniData() {
		$ret = array();

		$ret["datadir_path"] 	= $this->config->client()->dataDir();
		$ret["log_path"] 		= $this->config->log()->path()."/".$this->config->log()->fileName();
		$ret["error_log_path"] 	= $this->config->log()->errorLog();
		$ret["time_zone"] 		= $this->config->server()->timezone();
		$ret["convert_path"] 	= $this->config->tools()->convert();
		$ret["zip_path"] 		= $this->config->tools()->zip();
		$ret["unzip_path"] 		= $this->config->tools()->unzip();
		$ret["java_path"] 		= $this->config->tools()->java();
		$ret["setup_pass"] 		= $this->config->setup()->masterPassword();
		if($this->config->httpsAutoDetect()) {
			$ret["auto_https_detect_enabled"] 		= $this->config->httpsAutoDetect()->enabled();
			$ret["auto_https_detect_header_name"] 	= $this->config->httpsAutoDetect()->headerName();
			$ret["auto_https_detect_header_value"] 	= $this->config->httpsAutoDetect()->headerValue();
		}
		return $ret;
	}

	protected function getClientIniData() {
		$ret = array();
		$ret["datadir_path"] 	= $this->config->client()->dataDir();
		$ret["client_id"] 		= $this->config->client()->name();
		$ret["db_host"] 		= $this->config->database()->host();
		$ret["db_name"] 		= $this->config->database()->database();
		$ret["db_user"] 		= $this->config->database()->user();
		$ret["db_pass"] 		= $this->config->database()->password();
		$ret["db_type"] 		= $this->config->database()->engine();
		$ret["session_expire"] 	= $this->config->client()->sessionExpire();

		return $ret;
	}
}
