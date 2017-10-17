<?php
namespace CaT\Ilse\Action;

use CaT\Ilse\Config;
use CaT\Ilse\Aux;
use CaT\Ilse\Aux\Git;
use CaT\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\Yaml;
use CaT\Ilse\Aux\UpdatePluginsHelper;
use CaT\Ilse\Action\UpdatePlugin;

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
	 * @var UpdatePlugins
	 */
	protected $update_plugins_helper;

	/**
	 * @var UpdatePlugins
	 */
	protected $update_plugins;

	/**
	 * @var Yaml
	 */
	protected $parser;


	/**
	 * Constructor of the class UpdatePluginsDirectory
	 */
	public function __construct(
		Aux\Filesystem $filesystem,
		Git\GitFactory $factory,
		TaskLogger $task_logger,
		UpdatePluginsHelper $update_plugins_helper,
		UpdatePlugins $update_plugins
	) {
		$this->filesystem = $filesystem;
		$this->factory = $factory;
		$this->task_logger = $task_logger;
		$this->update_plugins_helper = $update_plugins_helper;
		$this->update_plugins = $update_plugins;
	}

	/**
	 * @inheritdoc
	 *
	 * @return void
	 */
	public function perform()
	{
		$this->initPluginDir();
		$this->clonePlugins();
		$this->updatePlugins();
		$this->deleteUnlistedPlugins();
		$this->linkPluginsToIlias();
	}

	/**
	 * Initialize the plugin directory.
	 *
	 * @return void
	 */
	protected function initPluginDir()
	{
		if(!$this->filesystem->exists($this->update_plugins_helper->dir()))
		{
			$this->task_logger->always("Make plugin directory", function ()
				{
					$this->filesystem->makeDirectory($this->update_plugins_helper->dir());
				});
		}
		$this->task_logger->always("Check write permissions", function()
			{
				if(!$this->filesystem->isWriteable($this->update_plugins_helper->dir()))
				{
					throw new \Exception("No write permissions");
				}
			});
	}

	/**
	 * Clone plugins
	 *
	 * @return void
	 */
	protected function clonePlugins()
	{
		$installed_plugins = $this->update_plugins_helper->getInstalledPlugins();
		$urls = $this->update_plugins_helper->getRepoUrls();
		$this->task_logger->eventually("Clone new plugins", function () use($urls, $installed_plugins)
			{
				foreach ($urls as $url)
				{
					$name = $this->update_plugins_helper->getRepoNameFromUrl($url);
					if(in_array($name, $installed_plugins))
					{
						continue;
					}
					$this->filesystem->makeDirectory($this->update_plugins_helper->dir()."/".$name);
					$git = $this->factory->getRepo($this->update_plugins_helper->dir()."/".$name, $url);
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
		$urls = $this->update_plugins_helper->getRepoUrls();
		$this->task_logger->eventually("Pull plugins", function () use($urls)
			{
				foreach ($urls as $url)
				{
					$name = $this->update_plugins_helper->getRepoNameFromUrl($url);
					$git = $this->factory->getRepo($this->update_plugins_helper->dir().'/'.$name, $url);
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
		$urls = $this->update_plugins_helper->getRepoUrls();
		$installed_plugins = $this->update_plugins_helper->getInstalledPlugins();
		$marked_plugins = $this->update_plugins_helper->getUnlistedPlugins($installed_plugins, $urls);

		$this->task_logger->eventually("Delete plugins", function () use($marked_plugins)
			{
				foreach($marked_plugins as $marked_plugin)
				{
					$this->task_logger->always("delete plugin $marked_plugin", function() use($marked_plugin)
						{
							$link = $this->update_plugins_helper->getPluginLinkPath($marked_plugin);
							$this->update_plugins->uninstall($link['name']);
							$this->filesystem->remove($link['path']."/".$link['name']);
							$this->filesystem->remove($this->update_plugins_helper->dir()."/".$marked_plugin);
						});
				}
			});
	}

	/**
	 * Link plugins to ilias
	 */
	protected function linkPluginsToIlias()
	{
		$this->task_logger->eventually("Link plugins", function ()
			{
				$installed_plugins = $this->update_plugins_helper->getInstalledPlugins();
				foreach($installed_plugins as $plugin)
				{
					$link = $this->update_plugins_helper->getPluginLinkPath($plugin);

					if(!$this->filesystem->exists($link['path']))
					{
						$this->filesystem->makeDirectory($link['path']);
					}
					if(!$this->filesystem->isWriteable($link['path']))
					{
						throw new \Exception("No write acces to ".$link['path'].".");
					}
					$this->task_logger->always("link plugin ".$link['name'], function() use($plugin, $link)
						{
							if($this->filesystem->isLink($link['path']."/".$link['name']))
							{
								return true;
							}
							$this->filesystem->symlink($this->update_plugins_helper->dir()."/".$plugin, $link['path']."/".$link['name']);
						});
				}
			});
	}
}