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
	 * Get an instance of installed plugin
	 *
	 * @param  	string 		$name
	 * @return 	object
	 */
	public function getPluginObject($name);

}