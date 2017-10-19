<?php
/* Copyright (c) 2016, 2017 Daniel Weise <daniel.weise@concepts-and-training.de> Extended GPL, see LICENSE */

namespace CaT\Ilse\Setup;

use CaT\Ilse\Config;
use Cat\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\UpdatePluginsHelper;

/**
 * Provides a PluginAdministration object depending on a given version number.
 */
class PluginAdministrationFactory {
	/**
	 * @param	string	$version
	 * @return	CoreInstaller
	 */
	public function getPluginAdministrationForRelease(
		$version,
		Config\General $config,
		TaskLogger $logger,
		UpdatePluginsHelper $update_plugin_helper
	) {
		assert('is_string($version)');

		if (substr($version, 0, 3) == "5.2") {
			return new PluginAdministration52($config, $logger, $update_plugin_helper);
		}
		throw new \InvalidArgumentException("There is no core installer for version '$version'");
	}
}