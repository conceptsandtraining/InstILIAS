<?php
namespace CaT\Ilse\Action;

use CaT\Ilse\Config;
use CaT\Ilse\Aux;
use CaT\Ilse\Aux\Git;
use CaT\Ilse\Aux\TaskLogger;

/**
 * Class UpdatePluginsDirectory
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
class UpdatePluginsDirectory implements Action
{
	const BRANCH = "master";

	/**
	 * @var array
	 */
	protected $server;

	/**
	 * @var array
	 */
	protected $plugins;

	/**
	 * @var Aux\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var Git\GitFactory
	 */
	protected $factory;

	/**
	 * @var TaskLogger
	 */
	protected $task_logger;

	/**
	 * @var string
	 */
	protected $dir;

	/**
	 * @var UpdatePlugins
	 */
	protected $update_plugins;

	/**
	 * @var string[]
	 */
	protected $installed_plugins = array();


	/**
	 * Constructor of the class UpdatePluginsDirectory
	 */
	public function __construct(Config\Server $server,
								Config\Plugins $plugins,
								Aux\Filesystem $filesystem,
								Git\GitFactory $factory,
								TaskLogger $task_logger,
								UpdatePlugins $update_plugins)
	{
		$this->server = $server;
		$this->plugins = $plugins;
		$this->filesystem = $filesystem;
		$this->factory = $factory;
		$this->task_logger = $task_logger;
		$this->update_plugins = $update_plugins;
	}

	/**
	 * @inheritdoc
	 *
	 * @return void
	 */
	public function perform()
	{
		$this->dir = $this->plugins->dir();

		$this->initPluginDir();
		$this->checkForInstalledPlugins();
		$this->clonePlugins();
		$this->updatePlugins();
		$this->deleteUnlistedPlugins();
	}

	/**
	 * Initialize the plugin directory.
	 *
	 * @return void
	 */
	protected function initPluginDir()
	{
		if(!$this->filesystem->exists($this->dir))
		{
			$this->task_logger->always("Make plugin directory", function ()
				{
					$this->filesystem->makeDirectory($this->dir);
				});
		}
		$this->task_logger->always("Check write permissions", function()
			{
				if(!$this->filesystem->isWriteable($this->dir))
				{
					throw new \Exception("No write permissions");
				}
			});
	}

	/**
	 * Check which plugins are installed.
	 *
	 * @return void
	 */
	protected function checkForInstalledPlugins()
	{
		if($this->filesystem->exists($this->dir))
		{
			$this->task_logger->always("Get installed plugins", function ()
				{
					$this->installed_plugins = $this->filesystem->getSubdirectories($this->dir);
				});
		}
	}

	/**
	 * Clone plugins
	 *
	 * @return void
	 */
	protected function clonePlugins()
	{
		$urls = $this->getRepoUrls();
		$this->task_logger->eventually("Clone new plugins", function () use($urls)
			{
				foreach ($urls as $url)
				{
					$name = $this->getRepoNameFromUrl($url);
					if(is_null($this->installed_plugins) || in_array($name, $this->installed_plugins))
					{
						continue;
					}
					$this->filesystem->makeDirectory($this->dir."/".$name);
					$git = $this->factory->getRepo($this->dir."/".$name, $url);
					$this->task_logger->always("clone plugin $name", [$git, "gitClone"]);
				}
			});
	}

	/**
	 * Update plugins
	 *
	 * @return void
	 */
	protected function updatePlugins()
	{
		$urls = $this->getRepoUrls();
		$this->task_logger->eventually("Pull plugins", function () use($urls)
			{
				foreach ($urls as $url)
				{
					$name = $this->getRepoNameFromUrl($url);
					$git = $this->factory->getRepo($this->dir.'/'.$name, $url);
					$this->task_logger->always("pull plugin $name", function() use($git)
						{
							$git->gitPull(self::BRANCH);
						});
				}
			});
	}

	/**
	 * Delete plugins, that not listed in the config file
	 *
	 * @return void
	 */
	protected function deleteUnlistedPlugins()
	{
		$urls = $this->getRepoUrls();
		$marked_plugins = $this->getUnlistedPlugins($this->installed_plugins, $urls);

		$this->task_logger->eventually("Delete plugins", function () use($marked_plugins)
			{
				foreach($marked_plugins as $marked_plugin)
				{
					$this->task_logger->always("delete plugin $marked_plugin", function() use($marked_plugin)
						{
							$this->update_plugins->uninstall($marked_plugin);
							$this->filesystem->remove($this->dir."/".$marked_plugin);
						});
				}
			});
	}

	/**
	 * Get unlisted plugins
	 *
	 * @return string[]
	 */
	protected function getUnlistedPlugins(array $installed, array $listed)
	{
		$listed = array_map(function($url) { return $this->getRepoNameFromUrl($url); }, $listed);
		return array_diff($installed, $listed);
	}

	/**
	 * Get repo urls
	 *
	 * @return string[]
	 */
	protected function getRepoUrls()
	{
		$ret = array_map(function($plugin)
			{
				return $plugin->git()->url();
			}
			, $this->plugins->plugins());
		return $ret;
	}

	/**
	 * Get the repo name
	 *
	 * @param 	string 	$url
	 * @return 	string
	 */
	protected function getRepoNameFromUrl($url)
	{
		assert('is_string($url)');

		$lastslash = strrpos($url, '/');
		return substr($url, $lastslash+1);
	}
}