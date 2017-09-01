<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Executer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use CaT\Ilse\App;

/**
 * Run the ILIAS installation process
 */
class InstallILIAS extends BaseExecuter
{
	/**
	 * @var \CaT\Ilse\IliasReleaseInstaller
	 */
	protected $iinst;

	/**
	 * @var ilDBUpdate
	 */
	protected $db_updater;

	/**
	 * @var ilLanguage
	 */
	protected $lng;

	/**
	 * @var $ilDB
	 */
	protected $db;

	/**
	 * Constructor of the class InstallILIAS
	 *
	 * @param string 									$config
	 * @param \CaT\Ilse\Interfaces\RequirementChecker 	$checker
	 * @param \CaT\Ilse\Interfaces\Git 					$git
	 * @param \CaT\Ilse\Interfaces\Pathes 				$path
	 */
	public function __construct($config,
								\CaT\Ilse\Interfaces\RequirementChecker $checker,
								\CaT\Ilse\Interfaces\Git $git,
								\CaT\Ilse\Interfaces\Pathes $path)
	{
		assert('is_string($config)');
		parent::__construct($config, $checker, $git, $path);

		chdir($this->absolute_path);
		if(file_exists($this->absolute_path.'/libs/composer/vendor/autoload.php'))
		{
			include_once $this->absolute_path.'/libs/composer/vendor/autoload.php';
		}

	}

	/**
	 * Start the installation process
	 */
	public function run()
	{
		$this->initILIAS();
		$this->initInstaller();
		$this->writeIliasIni();
		$this->writeClientIni();
		$this->checkSessionLifetime();
		$this->createDB();
		$this->applyingUpdates();
		$this->installLanguages();
		$this->checkAfterInstall();
		$this->setUserAndGroup();
	}

	/**
	 * Initialize ILIAS
	 */
	protected function initILIAS()
	{
		// my_setup_header has a high error risk.
		// It defines a lot of constant or objects
		// and requires ILIAS files.
		// Unfortunately it is required to do these steps,
		// or the installation will not run.
		echo "Initializing ILIAS...";
		$sh = new IlseSetupHeader($this->http_path,
								$this->absolute_path,
								$this->data_path,
								$this->web_dir,
								$this->client_id);
		$this->lng = $sh->initLanguage();
		$sh->init();
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Initialitze the IliasReleaseInstaller
	 */
	protected function initInstaller()
	{
		$setup = new \ilSetup(true,"admin");
		echo "Initializing installer...";
		$this->iinst = new \CaT\Ilse\IliasReleaseInstaller($setup, $this->gc);
		echo "\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Start installing with creating IliasIni
	 */
	protected function writeIliasIni()
	{
		echo "\nStart installing ILIAS\n";
		echo "Creating ilias.ini...";
		$this->iinst->writeIliasIni();
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Write client ini
	 */
	protected function writeClientIni()
	{
		echo "Creating client.ini...";
		$this->iinst->writeClientIni();
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Check session lifetime
	 */
	protected function checkSessionLifetime()
	{
		echo "Check session lifetime...";
		if(!$this->iinst->checkSessionLifeTime()) {
			echo "\n\t\tYour session max lifetime in php.ini is smaller then ILIAS lifetime.\n"
				."\t\tPlease change it or ILIAS lifetime will never be used.\n";
		}
		echo "\t\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Create the database
	 */
	protected function createDB()
	{
		$this->iinst->connectDatabase();

		echo "Creating database...";
		$this->iinst->installDatabase();
		$this->db = $this->iinst->getDatabaseHandle();
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		$this->db_updater = new \ilDBUpdate($this->db);
	}

	/**
	 * Applying updates to the db
	 */
	protected function applyingUpdates()
	{
		echo "Applying updates...";
		$this->iinst->applyUpdates($this->db_updater);
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		echo "Applying hotfixes...";
		$this->iinst->applyHotfixes($this->db_updater);
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Installing languages
	 */
	protected function installLanguages()
	{
		echo "Installing languages...";
		$this->lng->setDbHandler($this->db);
		$this->iinst->installLanguages($this->lng);
		echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
	}

	/**
	 * Check after install
	 */
	protected function checkAfterInstall()
	{
		$this->iinst->setProxy();
		$this->iinst->registerNoNic();

		if(!$this->iinst->finishSetup()) {
			throw new \RuntimeException("Something went wrong.");
		}

		echo "\nILIAS successfull installed.";
	}

	/**
	 * Ensure all directorys owned by specified user and group
	 */
	protected function setUserAndGroup()
	{
		$p 		= new Process("");
		$pathes = [$this->data_path, $this->absolute_path, $this->error_log];

		array_map(function ($path) use ($p) {
			$p->setCommandLine("chown -R ".$this->user.":".$this->group." ".$path);
			$p->setTty(true);
			$p->run();
		}, $pathes);
	}
}