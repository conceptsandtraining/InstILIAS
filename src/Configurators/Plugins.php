<?php

namespace CaT\InstILIAS\Configurators;

/**
 * Configurate ILIAS plugins part
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

	public function __construct($absolute_path, \ilDB $db) {
		$this->gDB = $db;
		$this->absolute_path = $absolute_path;
	}

	/**
	 * @inheritdoc
	 */
	public function installPlugins(\CaT\InstILIAS\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\InstILIAS\IliasPluginInstaller($this->absolute_path, $this->gDB);
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
	public function activatePlugins(\CaT\InstILIAS\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\InstILIAS\IliasPluginInstaller($this->absolute_path, $this->gDB);
		foreach ($plugins->plugins() as $plugin) {
			$plugin_installer->activate($plugin);
			$plugin_installer->updateLanguage($plugin);
		}
		$plugin_installer = null;
	}

	/**
	 * @inheritdoc
	 */
	public function updatePlugins(\CaT\InstILIAS\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\InstILIAS\IliasPluginInstaller($this->absolute_path, $this->gDB);
		foreach ($plugins->plugins() as $plugin) {
			$plugin_installer->update($plugin);
			$plugin_installer->updateLanguage($plugin);
		}
		$plugin_installer = null;
	}

	/**
	 * Uninstalls a plugin
	 *
	 * @param \CaT\InstILIAS\Config\Plugins 	$plugins
	 */
	public function uninstallPlugins(\CaT\InstILIAS\Config\Plugins $plugins) {
		$plugin_installer = new \CaT\InstILIAS\IliasPluginInstaller($this->absolute_path, $this->gDB);
		foreach ($plugins->plugins() as $plugin) {
			
			$plugin_installer->uninstall($plugin->name());
		}
		$plugin_installer = null;
	}
}