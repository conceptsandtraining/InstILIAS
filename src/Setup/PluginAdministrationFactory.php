<?php
/* Copyright (c) 2016, 2017 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Setup;

use CaT\Ilse\Config;
use Cat\Ilse\Aux\TaskLogger;

class PluginAdministrationFactory {
	/**
	 * @param	string	$version
	 * @return	CoreInstaller
	 */
	public function getPluginAdministrationForRelease($version, Config\General $config, TaskLogger $logger) {
		if (substr($version, 0, 3) == "5.2") {
			return new PluginAdministration52($config, $logger);
		}
		throw new \InvalidArgumentException("There is no core installer for version '$version'");
	}
}