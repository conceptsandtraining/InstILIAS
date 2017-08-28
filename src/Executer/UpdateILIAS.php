<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Executer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use CaT\Ilse\App;

/**
 * Run the ILIAS update process
 */
class UpdateILIAS extends BaseExecuter
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
	 * @param \CaT\Ilse\Interfaces\Pathes 		$path
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
	 * Start the update process
	 */
	public function run()
	{
		$this->initILIAS();
		$this->initInstaller();
		$this->cloneILIAS();
		$this->updateDatabase();
		$this->applyingUpdates();
		$this->applyingHotfixes();
		$this->updateLanguages();
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
		echo "\t\t\tDone!\n";
	}

	/**
	 * Initialitze the IliasReleaseInstaller
	 */
	protected function initInstaller()
	{
		$setup = new \ilSetup(true,"admin");
		echo "Initializing installer...";
		$this->iinst = new \CaT\Ilse\IliasReleaseInstaller($setup, $this->gc);
		$this->iinst->newClient($this->client_id);
		$this->iinst->connectDatabase();
		echo "\t\tDone!\n";
	}

	/**
	 * Clone ILIAS
	 */
	protected function cloneILIAS()
	{
		echo "Updating ILIAS Code from\n";
		echo "(This could take a few minutes)...";
		$this->git->cloneGitTo($this->git_url, $this->git_branch_name, $this->absolute_path, "");
		echo "\tDone!\n";
	}

	/**
	 * Update database
	 */
	protected function updateDatabase()
	{
		echo "\nUpdating database...";
		$this->db = $this->iinst->getDatabaseHandle();
		$this->db_updater = new \ilDBUpdate($this->db);
		echo "\t\t\tDone!\n";
	}

	/**
	 * Applying updates
	 */
	protected function applyingUpdates()
	{
		echo "Applying updates...";
		$this->iinst->applyUpdates($this->db_updater);
		echo "\t\t\tDone!\n";
	}

	/**
	 * Applying hotfixes
	 */
	protected function applyingHotfixes()
	{
		echo "Applying hotfixes...";
		$this->iinst->applyHotfixes($this->db_updater);
		echo "\t\t\tDone!\n";
	}

	/**
	 * Update languages
	 */
	protected function updateLanguages()
	{
		echo "Updating languages...";
		$this->lng->setDbHandler($this->db);
		$this->iinst->installLanguages($this->lng);
		echo "\t\t\tDone!\n";
	}
}