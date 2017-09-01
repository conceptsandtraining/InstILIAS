<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Executor;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use CaT\Ilse\App;

/**
 * Run the ILIAS update process
 */
class UpdatePluginsILIAS extends BaseExecutor
{
	/**
	 * @var CaT|Configurators\Plugins
	 */
	protected $pc;

	/**
	 * Constructor of the class InstallILIAS
	 *
	 * @param string 									$config
	 * @param \CaT\Ilse\Interfaces\RequirementChecker 	$checker
	 * @param \CaT\Ilse\Interfaces\Git 					$git
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
		$this->initConfigurator();
		$this->updatePlugins();
		$this->deletePlugins();
	}

	/**
	 * Initialize the PluginConfigurator
	 */
	protected function initConfigurator()
	{
		$ic = new \CaT\Ilse\IliasReleaseConfigurator($this->absolute_path, $this->client_id);
		$this->pc = $ic->getPluginsConfigurator();
		echo "Initialize plugin configurator...";
		echo "\tDone!\n";
	}

	/**
	 * Update plugins
	 */
	protected function updatePlugins()
	{
		if($this->gc->plugin() !== null)
		{
			echo "\nUpdating plugins...\n";
			$this->pc->installPlugins($this->gc->plugin());
			$this->pc->updatePlugins($this->gc->plugin());
			$this->pc->activatePlugins($this->gc->plugin());
			echo "\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Delete plugins
	 */
	protected function deletePlugins()
	{
		echo "\nUninstalling plugins...";
		$this->pc->uninstallPlugins($this->gc->plugin());
		echo "\t\t\tDone!\n";
	}
}