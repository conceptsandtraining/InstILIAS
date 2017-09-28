<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\App;

use CaT\Ilse\Action;
use CaT\Ilse\Aux;
use CaT\Ilse\Aux\Git;
use CaT\Ilse\Setup\CoreInstallerFactory;

use Pimple\Container;
use Symfony\Component\Console\Application;

/**
 * Do the main initializing
 */
class App extends Application
{
	const CONFIG_REPOS_DIR	= "ilias-configs";
	const ILSE_CONFIG		= "config.yaml";
	const ILSE_DIR			= ".ilse";
	const I_F_CONFIG		= "ilse_config.yaml";
	const I_R_BRANCH		= "master";

	public function __construct() {
		parent::__construct("ilse - automatically sets ILIAS up");
		$this->initCommands();
	}

	/**
	 * Initialize the dependency injection container.
	 *
	 * @return Container
	 */
	public function getDIC() {
		$container = new Container();

		// Actions

		$container["action.initAppFolder"] = function($c) {
			return new Action\InitAppFolder
						( self::ILSE_DIR
						, self::ILSE_CONFIG
						, function () use ($c) { return $c["aux.configRepoLoader"]; }
						, $c["aux.filesystem"]
						, $c["aux.taskLogger"]
						);
		};
		$container["action.deleteILIAS"] = function($c) {
			$config = $c["config.ilias"];
			return new Action\DeleteILIAS
						( $config->database()
						, $config->server()
						, $config->client()
						, $config->log()
						, $c["aux.filesystem"]
						, $c["aux.taskLogger"]
						);
		};
		$container["action.installILIAS"] = function($c) {
			return new Action\InstallILIAS
						( $c["config.ilias"]
						, $c["setup.coreInstallerFactory"]
						, $c["aux.taskLogger"]
						);
		};
		$container["action.buildInstallationEnvironment"] = function($c) {
			$config = $c["config.ilias"];
			return new Action\BuildInstallationEnvironment
						( $config->server()
						, $config->client()
						, $config->database()
						, $config->log()
						, $config->git()
						, $c["action.checkRequirements"]
						, $c["aux.taskLogger"]
						, $c["aux.gitFactory"]
						, $c["aux.filesystem"]
						);
		};
		$container["action.checkRequirements"] = function($c) {
			$config = $c["config.ilias"];
			return new Action\CheckRequirements
						( $config->server()
						, $config->client()
						, $config->git()
						, $config->database()
						, $config->log()
						, $c["aux.filesystem"]
						, $c["aux.taskLogger"]
						);
		};

		// Configs

		$container["config.ilias"] = function($c) {
			throw new \RuntimeException("Expected command to initialize ILIAS config.");
		};
		$container["config.ilse"] = function($c) {
			return $this->readAppConfigFile
						( $c["aux.filesystem"]
						, $c["aux.configParser"]
						, $c["aux.taskLogger"]
						);
		};

		// Auxiliary Services

		$container["aux.filesystem"] = function($c) {
			return new Aux\FilesystemImpl();
		};
		$container["aux.taskLogger"] = function($c) {
			throw new \RuntimeException("Expected command to initialize task logger.");
		};
		$container["aux.configLoader"] = function($c) {
			return new Aux\ConfigLoaderTemp
						( $c["aux.configMerger"]
						, $c["aux.configParser"]
						, $c["aux.filesystem"]
						, function () use ($c) { return $c["aux.configRepoLoader"]; }
						);
		};
		$container["aux.configRepoLoader"] = function($c) {
			$path = $c["aux.filesystem"]->homeDirectory()."/".self::ILSE_DIR;
			return new Aux\ConfigRepoLoaderTemp
						( $path
						, $c["config.ilse"]["repos"]
						, $c["aux.filesystem"]
						, $c["aux.taskLogger"]
						, $c["aux.gitFactory"]
						);
		};
		$container["aux.configMerger"] = function($c) {
			return new Aux\ConfigMerger();
		};
		$container["aux.configParser"] = function($c) {
			return new Aux\YamlConfigParser();
		};
		$container["aux.gitFactory"] = function($c) {
			return new Git\GitFactory();
		};

		// Setup

		$container["setup.coreInstallerFactory"] = function($c) {
			return new CoreInstallerFactory();
		};

		return $container;
	}

	/**
	 * Initialize commands and add them to the app.
	 *
	 * @return	void
	 */
	protected function initCommands()
	{
		$dic = $this->getDIC();

//		$this->add(new Command\UpdateCommand($path, $merger, $checker, $git, $repos));
		$this->add(new Command\DeleteCommand($dic));
		$this->add(new Command\UpdatePluginsCommand($dic));
//		$this->add(new Command\ReinstallCommand($path, $merger, $checker, $git, $repos));
		$this->add(new Command\InstallCommand($dic));
//		$this->add(new Command\ConfigCommand($path, $merger, $checker, $git, $repos));
		$this->add(new Command\ExampleConfigCommand($dic));
	}

	/**
	 * Initialize the config repo in ~/.ilias-installer/config
	 *
	 * @param string 				$path
	 * @param Git\Git 		$gw
	 * @param Interfaces\Parser 	$parser
	 * @param string 				$repos
	 * @param GitExecutor 			$ge
	 */
	protected function initConfigRepo($path, Git\Git $gw, Interfaces\Parser $parser, $repos, GitExecutor $ge)
	{
		$name = "";
		$path = $path->getHomeDir() . "/" . self::I_P_GLOBAL_CONFIG;

		foreach ($repos as $repo)
		{
			$dir = $this->getUniqueDirName($path, $repo);
			if(!is_dir($dir))
			{
				mkdir($dir, 0755, true);
			}
			else
			{
				$name = basename($repo, '.git');
			}
			$ge->cloneGitTo($repo,
							self::I_R_BRANCH,
							$dir,
							$name
							);
		}
	}

	/**
	 * Read app config file
	 *
	 * @param Aux\Filesystem		$fs
	 * @param Aux\YamlConfigParser	$parser
	 * @param Aux\TaskLogger		$logger
	 *
	 * @return array
	 */
	protected function readAppConfigFile(Aux\Filesystem	$fs, Aux\YamlConfigParser $parser, Aux\TaskLogger $logger)
	{
		return $logger->always("Read ilse config file", function() use ($fs, $parser, $logger) {
			$path = $fs->homeDirectory()."/".self::ILSE_DIR."/".self::ILSE_CONFIG;
			if (!$fs->exists($path)) {
				throw new \RuntimeException("ilse config file not found at '$path'.");
			}
			return $parser->read($path);
		});
	}
}
