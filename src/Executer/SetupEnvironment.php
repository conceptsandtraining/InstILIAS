<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Executor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use CaT\Ilse\App;

/**
 * Run the ILIAS setup
 */
class SetupEnvironment extends BaseExecutor
{
	/**
	 * @var bool
	 */
	protected $interactive;

	/**
	 * Constructor of the class SetupEnvironment
	 *
	 * @param string 									$config
	 * @param \CaT\Ilse\Interfaces\RequirementChecker 	$checker
	 * @param \CaT\Ilse\Interfaces\Git 					$git
	 * @param bool 										$interactive
	 * @param \CaT\Ilse\Interfaces\Pathes 				$path
	 */
	public function __construct($config,
								\CaT\Ilse\Interfaces\RequirementChecker $checker,
								\CaT\Ilse\Interfaces\Git $git,
								$interactive,
								\CaT\Ilse\Interfaces\Pathes $path)
	{
		assert('is_string($config)');
		assert('is_bool($interactive)');

		parent::__construct($config, $checker, $git, $path);
		$this->interactive = $interactive;
	}

	/**
	 * Start the setup for the environment
	 */
	public function run()
	{
		$this->crateDataDir();
		$this->checkDataDirPermissions();
		$this->checkDataDirEmpty();
		$this->createLogDir();
		$this->createErrorLogDir();
		$this->createLogFile();
		$this->checkPHPVersion();
		$this->checkPDO();
		$this->checkDBConnection();
		$this->validPhpForIliasBranch();
		$this->cloneILIAS();
	}

	/**
	 * Create data directory
	 */
	protected function crateDataDir()
	{
		$check = $this->checker->dataDirectoryExists($this->data_path);
		if($this->interactive && !$check)
		{
			echo "Data directory does not exist. Create the directory (yes|no)? ";
			$line = $this->getUserInput();
			if(strtolower($line) != "yes") {
				echo "Aborted by user.";
				exit(1);
			}

			echo "Creating data directory...";
			mkdir($this->data_path, 0770, true);
			echo "\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
		else if(!$this->interactive && !$check)
		{
			echo "Creating data directory...";
			mkdir($this->data_path, 0770, true);
			echo "\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Check data directory permissions
	 */
	protected function checkDataDirPermissions()
	{
		$check = $this->checker->dataDirectoryPermissions($this->data_path);
		if($this->interactive && !$check)
		{
			echo "Not enough permissions on data directory. Set permissions (yes|no)? ";
			$line = $this->getUserInput();
			if(strtolower($line) != "yes") {
				echo "Aborted by user.";
				exit(1);
			}

			echo "Setting permission to required...";
			chmod($this->data_path, 0770);
			echo "\t\t\t\t\t\tDone!\n";
		}
		else if(!$this->interactive && !$check)
		{
			echo "Setting permission to required...";
			chmod($this->data_path, 0770);
			echo "\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Check whether the data directory is empty
	 */
	protected function checkDataDirEmpty()
	{
		$check = $this->checker->dataDirectoryEmpty($this->data_path, $this->client_id, $this->web_dir);
		if($this->interactive && !$check)
		{
			echo "Data directory is not empty. Clean the directory (yes|no)? ";
			$line = $this->getUserInput();
			if(strtolower($line) != "yes") {
				echo "Aborted by user.";
				exit(1);
			}

			echo "Cleaning the directory ".$this->data_path."/".$this->client_id."...";
			$this->clearDirectory($this->data_path."/".$this->client_id);
			echo "\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Create log directory
	 */
	protected function createLogDir()
	{
		$check = $this->checker->logDirectoryExists($this->gc->log()->path());
		if($this->interactive && !$check)
		{
			echo "Log directory does not exist. Create the directory (yes|no)? ";
			$line = $this->getUserInput();
			if(strtolower($line) != "yes") {
				echo "Aborted by user.";
				exit(1);
			}

			echo "Creating log directory...";
			mkdir($this->gc->log()->path(), 0770, true);
			echo "\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
		else if(!$this->interactive && !$check)
		{
			echo "Creating log directory...";
			mkdir($this->gc->log()->path(), 0770, true);
			echo "\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Create error_log dir
	 */
	protected function createErrorLogDir()
	{
		$check = $this->checker->logDirectoryExists($this->gc->log()->error_log());
		if(!$check)
		{
			mkdir($this->gc->log()->error_log(), 0770, true);
		}
	}

	/**
	 * Create log file
	 */
	protected function createLogFile()
	{
		if(!$this->checker->logFileExists($this->gc->log()->path(), $this->gc->log()->fileName()))
		{
			touch($this->gc->log()->path()."/".$this->gc->log()->fileName());
			chmod($this->gc->log()->path()."/".$this->gc->log()->fileName(), 0640);
		}
	}

	/**
	 * Check for valid php version
	 *
	 * @throws \Exception
	 */
	protected function checkPHPVersion()
	{
		if(!$this->checker->validPHPVersion(phpversion(), "5.4"))
		{
			throw new \Exception("Your PHP Version is too old. Please update to 5.4 or higher.");
		}
	}

	/**
	 * Check for installed PDO
	 *
	 * @throws \Exception
	 */
	protected function checkPDO()
	{
		if(!$this->checker->pdoExist())
		{
			throw new \Exception("PDO is not installed.");
		}
	}

	/**
	 * Check db connection
	 *
	 * @throws \Exception
	 */
	protected function checkDBConnection()
	{
		if(!$this->checker->databaseConnectable($this->gc->database()->host(), $this->gc->database()->user(), $this->gc->database()->password()))
		{

			throw new \Exception("It's not possible to connect a MySQL database. Please ensure you have a MySQL database installed or started.");
		}
	}

	/**
	 * Check for valid php version for ILIAS branch
	 *
	 * @throws \Exception
	 */
	protected function validPhpForIliasBranch()
	{
		if(!$this->checker->phpVersionILIASBranchCompatible(phpversion(), $this->git_branch_name))
		{
			throw new \Exception("Your PHP Version (".phpversion().") is not compatible to the selected branch (".$this->git_branch_name.").");
		}
	}

	/**
	 * Clone ILIAS
	 */
	protected function cloneILIAS()
	{
		try {
			echo "Clone repository from ".$this->git_url;
			echo " (This could take a few minutes)...";
			$this->git->cloneGitTo($this->git_url,
								   $this->git_branch_name,
								   substr($this->absolute_path, 0, strrpos($this->absolute_path, '/')),
								   ""
								   );
			echo "\t\t\tDone!\n";
		} catch(\RuntimeException $e) {
			echo $e->getMessage();
			throw $e;
		}
	}

	/**
	 * Get user input from cli
	 */
	protected function getUserInput()
	{
		$handle = fopen ("php://stdin","r");
		$line = fgets($handle);
		return trim($line);
	}

	/**
	 * Remove all stuff in the named dir recursiv
	 *
	 * @param string 		$dir
	 */
	protected function clearDirectory($dir)
	{
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->clearDirectory("$dir/$file") : unlink("$dir/$file");
		}

		rmdir($dir);
	}
}