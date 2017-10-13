<?php
namespace CaT\Ilse\Action;

use CaT\Ilse\Aux;
use CaT\Ilse\Config;
use CaT\Ilse\Setup\PluginAdministrationFactory;


// delegieren an PluginAdmin
// links nach ilias erstellen

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
	 * Constructor of the class UpdatePlugins
	 */
	public function __construct(Config\General $config, PluginAdministrationFactory $factory, Aux\TaskLogger $logger)
	{
		$this->config = $config;
		$this->plugin_admin_factory = $factory;
		$this->logger = $logger;
	}

	/**
	 * @inheritdoc
	 */
	public function perform()
	{
		$this->install();
		$this->update();
		$this->activate();
		$this->link();
	}

	/**
	 * 
	 */
	protected function getPluginAdmin()
	{
		if(!$this->plugin_admin)
		{
			$this->plugin_admin = $this->plugin_admin_factory->getPluginAdministrationForRelease("5.2", $this->config, $this->logger);
		}
		return $this->plugin_admin;
	}

	/**
	 * Install plugins
	 */
	protected function install()
	{
		$this->logger->eventually("Install plugins", function () {
			foreach ($this->config->plugin() as $plugin) {
				return $this->getPluginAdmin()->install($plugin);
			}
		});
	}

	/**
	 * Update plugins
	 */
	protected function update()
	{
		$this->logger->always("Update plugins", [$this->getPluginAdmin(), "update"]);
	}

	/**
	 * Activate plugins
	 */
	protected function activate()
	{
		$this->logger->always("Activate plugins", [$this->getPluginAdmin(), "activate"]);
	}

	/**
	 * Delete plugin
	 */
	public function uninstall()
	{
		$this->logger->always("Uninstall plugin", [$this->getPluginAdmin(), "uninstall"]);
	}

	/**
	 * Link plugin to ilias
	 */
	protected function link()
	{

	}
}