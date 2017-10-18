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
	 * Creates a record in db for the specified plugin
	 *
	 * @param 	string 		$name
	 * @return 	void
	 */
	public function install($name);

	/**
	 * Update plugins
	 *
	 * @param 	string 		$name
	 * @return 	void
	 */
	public function update($name);

	/**
	 * Activate plugin
	 *
	 * @param 	$name
	 * @return 	void
	 */
	public function activate($name);

	/**
	 * Update language for a plugin
	 *
	 * @param 	string 		$name
	 * @return 	void
	 */
	public function updateLanguage($name);

	/**
	 * Uninstall plugin
	 *
	 * @param 	string 		$name
	 * @return 	void
	 */
	public function uninstall($name);

	/**
	 * Checks whether a plugin needs an update
	 *
	 * @param 	string 		$name
	 * @return 	bool
	 */
	public function needsUpdate($name);

	/**
	 * Get an instance of installed plugin
	 *
	 * @param  	string 		$name
	 * @return 	object
	 */
	public function getPluginObject($name);

}