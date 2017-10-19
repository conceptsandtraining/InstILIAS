<?php

/* Copyright (c) 2016, 2017 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Action;

use CaT\Ilse\Aux\ConfigRepoLoader;
use CaT\Ilse\Aux\Filesystem;
use CaT\Ilse\Aux\TaskLogger;

/**
 * Initialize the folder of the ilse if it doesn't yet exist.
 */
class InitAppFolder implements Action
{
	/**
	 * @var	string
	 */
	protected $folder_name;

	/**
	 * @var	string
	 */
	protected $config_name;

	/**
	 * @var ConfigRepoLoader
	 */
	protected $config_repo_loader = null;

	/**
	 * @var	callable
	 */
	protected $get_config_repo_loader;

	/**
	 * @var	Filesystem
	 */
	protected $filesystem;	

	/**
	 * @var TaskLogger
	 */
	protected $task_logger;

	/**
	 * @param	string		$folder_name
	 * @param	Filesystem $filesystem
	 */
	public function __construct($folder_name, $config_name, callable $get_config_repo_loader, Filesystem $filesystem, TaskLogger $logger)
	{
		assert('is_string($folder_name)');
		assert('is_string($config_name)');
		$this->folder_name = $folder_name;
		$this->config_name = $config_name;
		$this->get_config_repo_loader = $get_config_repo_loader;
		$this->filesystem = $filesystem;
		$this->task_logger = $logger;
	}

	/**
	 * Delete ILIAS.
	 *
	 * @return	void
	 */
	public function perform() {
		$fs = $this->filesystem;
		$dir = $fs->homeDirectory()."/".$this->folder_name;
		if (!$fs->exists($dir)) {
			$this->task_logger->always("Initializing ilse directory", function () use ($dir, $fs) {
				$this->task_logger->always("Creating directory $dir for ilse", function () use ($dir, $fs) {
					$fs->makeDirectory($dir);
				});
				$config_file = $dir."/".$this->config_name;
				$this->task_logger->always("Writing default config to $config_file", function() use ($config_file, $fs) {
					$default_config = $fs->read(__DIR__."/../../assets/ilse_default_config.yaml");
					$fs->write($config_file, $default_config);
				});
				$get_config_repo_loader  = $this->get_config_repo_loader;
				$this->config_repo_loader = $get_config_repo_loader();
				if (!($this->config_repo_loader instanceof ConfigRepoLoader)) {
					throw new \RuntimeException("Expected ConfigRepoLoader, got ".get_class($this->config_repo_loader));
				}
				$this->task_logger->always("Updating config repos", function() {
					$this->config_repo_loader->updateConfigRepos();
				});
			});
		}
	}
}
