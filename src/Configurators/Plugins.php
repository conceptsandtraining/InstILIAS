<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS plugins part
 * 
 * Create roles
 */
class Plugins {
	/**
	 * @var \ilDB
	 */
	protected $gDB;

	/**
	 * @var string
	 */
	protected $absolute_path;

	public function __construct($absolute_path, \ilDBInterface $db) {
		$this->gDB = $db;
		$this->absolute_path = $absolute_path;
	}

	/**
	 * @inheritdoc
	 */
	public function installPlugins(\CaT\Ilse\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\Ilse\IliasPluginInstaller($this->absolute_path, $this->gDB);
		foreach ($plugins->plugins() as $plugin) {
			if(!$plugin_installer->isInstalled($plugin)) {
				$plugin_installer->install($plugin);
			} else {
				$plugin_installer->updateBranch($plugin);
			}
		}

		$plugin_installer = null;
	}

	/**
	 * @inheritdoc
	 */
	public function activatePlugins(\CaT\Ilse\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\Ilse\IliasPluginInstaller($this->absolute_path, $this->gDB);
		foreach ($plugins->plugins() as $plugin) {
			$plugin_installer->activate($plugin);
			$plugin_installer->updateLanguage($plugin);
		}
		$plugin_installer = null;
	}

	/**
	 * @inheritdoc
	 */
	public function updatePlugins(\CaT\Ilse\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\Ilse\IliasPluginInstaller($this->absolute_path, $this->gDB);
		foreach ($plugins->plugins() as $plugin) {
			$plugin_installer->update($plugin);
			$plugin_installer->updateLanguage($plugin);
		}
		$plugin_installer = null;
	}

	/**
	 * Uninstalls a plugin
	 *
	 * @param \CaT\Ilse\Config\Plugins 	$plugins
	 */
	public function uninstallPlugins(\CaT\Ilse\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\Ilse\IliasPluginInstaller($this->absolute_path, $this->gDB);
		$config_plugin = array();

		if($plugins !== null) {
			$config_plugins = array_map(function($pl) { return $pl->name(); }, $plugins->plugins());
		}

		foreach ($plugin_installer->getInstalledPluginNames() as $installed_pl) {
			if(in_array($installed_pl, $config_plugins)) {
				continue;
			}

			$plugin_installer->createPluginRecord($installed_pl);
			$plugin_installer->uninstall($installed_pl);
			$plugin_installer->removeFiles($installed_pl);
		}

		$plugin_installer = null;
	}
}