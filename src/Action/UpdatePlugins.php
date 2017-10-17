<?php
namespace CaT\Ilse\Action;

use CaT\Ilse\Config\General;
use CaT\Ilse\Setup\PluginAdministrationFactory;
use CaT\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\UpdatePluginsHelper;

/**
 * Install or update plugins from a list.
 * It also delete plugins that are installed and now absence on the list.
 * Then it activate the plugins and updates the language.
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
class UpdatePlugins implements Action
{
	/**
	 * PluginAdministration
	 */
	protected $plugin_admin;

	/**
	 * @var Config\General
	 */
	protected $config;

	/**
	 * @var PluginAdministrationFactory
	 */
	protected $plugin_admin_factory;

	/**
	 * @var UpdatePluginsDirectory
	 */
	protected $update_plugins_helper;

	/**
	 * @var TaskLogger
	 */
	protected $logger;

	/**
	 * Constructor of the class UpdatePlugins
	 */
	public function __construct(
		General $config,
		PluginAdministrationFactory $factory,
		UpdatePluginsHelper $update_plugins_helper,
		TaskLogger $logger
	) {
		$this->config = $config;
		$this->plugin_admin_factory = $factory;
		$this->update_plugins_helper = $update_plugins_helper;
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function perform()
	{
		$urls = $this->update_plugins_helper->getRepoUrls();

		$this->install($urls);
		$this->update($urls);
		$this->activate($urls);
		$this->updateLanguage($urls);
	}

	/**
	 * Get an instance of PluginAdministration
	 *
	 * @return PluginAdministration
	 */
	protected function getPluginAdmin()
	{
		if(!$this->plugin_admin)
		{
			$this->plugin_admin = $this->plugin_admin_factory->getPluginAdministrationForRelease(
				"5.2",
				$this->config,
				$this->logger,
				$this->update_plugins_helper
				);
		}
		return $this->plugin_admin;
	}

	/**
	 * Install plugins
	 *
	 * @param 	string[]	$urls
	 * @return 	void
	 */
	protected function install($urls)
	{
		assert('is_array($urls)');
		$this->logger->eventually("Install plugin", function() use($urls) {
			foreach ($urls as $url) {
				$name = substr($url, strrpos($url, "-")+1);
				$this->logger->always("install plugin ".$name, function() use($name) {
					$this->getPluginAdmin()->install($name);
				});
			}
		});
	}

	/**
	 * Update plugins
	 *
	 * @param 	string[]	$urls
	 * @return 	void
	 */
	protected function update($urls)
	{
		assert('is_array($urls)');
		$this->logger->eventually("Update plugin", function() use($urls) {
			foreach ($urls as $url) {
				$name = substr($url, strrpos($url, "-")+1);
				$this->logger->always("update plugin ".$name, function() use($name) {
					if($this->getPluginAdmin()->needsUpdate($name)) {
						$this->getPluginAdmin()->update($name);
					}
				});
			}
		});
	}

	/**
	 * Activate plugins
	 *
	 * @param 	string[]	$urls
	 * @return 	void
	 */
	protected function activate($urls)
	{
		$this->logger->eventually("Activate plugin", function() use($urls) {
			foreach ($urls as $url) {
				$name = substr($url, strrpos($url, "-")+1);
				$this->logger->always("activate plugin ".$name, function() use($name) {
					$this->getPluginAdmin()->activate($name);
				});
			}
		});
	}

	/**
	 * Update language
	 *
	 * @param 	string[]	$urls
	 * @return 	void
	 */
	protected function updateLanguage($urls)
	{
		$this->logger->eventually("Update plugin language", function() use($urls) {
			foreach ($urls as $url) {
				$name = substr($url, strrpos($url, "-")+1);
				$this->logger->always("update language for plugin ".$name, function() use($name) {
					$this->getPluginAdmin()->updateLanguage($name);
				});
			}
		});
	}

	/**
	 * Delete plugin
	 *
	 * @param 	$name
	 * @return 	void
	 */
	public function uninstall($name)
	{
		assert('is_string($name)');
		$this->getPluginAdmin()->uninstall($name);
	}
}