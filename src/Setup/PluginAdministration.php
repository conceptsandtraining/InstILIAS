<?php
namespace CaT\Ilse\Setup;

/**
 * Interface Plugin
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
interface PluginAdministration
{
	/**
	 * Install plugins
	 *
	 * @param 	string 		$plugin
	 * @throws 	RuntimeException
	 * @return 	void
	 */
	public function install($plugin);

	/**
	 * Checks whether a plugin is installed
	 *
	 * @param 	string 		$plugin
	 * @return 	void
	 */
	public function isInstalled($plugin);

	/**
	 * Update plugins
	 *
	 * @param 	string 		$plugin
	 * @throws 	RuntimeException
	 * @return 	void
	 */
	public function update();

	/**
	 * Activate plugin
	 *
	 * @param 	$plugin
	 * @throws 	RuntimeException
	 * @return 	void
	 */
	public function activate();

	/**
	 * Deactivate plugin
	 *
	 * @param 	string 		$plugin
	 * @throws 	RuntimeException
	 * @return 	void
	 */
	public function deactivate($plugin);

	/**
	 * Update language for a plugin
	 *
	 * @param 	string 		$plugin
	 */
	public function updateLanguage($plugin);

	/**
	 * Get an instance of installed plugin
	 *
	 * @param  	string 		$plugin
	 * @throws 	RuntimeException
	 * @return 	object
	 */
	public function getPluginObject($plugin);

	/**
	 * Uninstall plugin
	 *
	 * @param 	string 		$plugin
	 * @throws 	RuntimeException
	 * @return 	void
	 */
	public function uninstall();

	/**
	 * Check wether the plugin needs an update
	 *
	 * @param 	string 		$plugin
	 * @return 	bool
	 */
	public function needsUpdate($plugin);
}