<?php

/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

use PHPUnit\Framework\TestCase;
use CaT\Ilse\Aux\UpdatePluginsHelper;
use CaT\Ilse\Config\Server;
use CaT\Ilse\Config\Plugin;
use CaT\Ilse\Config\Plugins;
use CaT\Ilse\Config\Git;
use CaT\Ilse\Aux\Filesystem;
use CaT\Ilse\Aux\Yaml;

// If database had it own interface like filesystem, we could
// drop this and write a proper test instead.
class UpdatePluginsHelperForTest extends UpdatePluginsHelper{
}

class UpdatePluginsHelperTest extends TestCase
{
	public function setUp()
	{
		$path = "test/dummy";
		$url = "https://my_plugin";

		$server = new Server("http://ilias.de", "/var/www/html/ilias", "Europe/Berlin");
		$git = new Git($url, "master", "5355");
		$plugin = new Plugin($path, $git);
		$plugins = new Plugins($path, array($plugin));
		$filesystem = $this->createMock("CaT\Ilse\Aux\Filesystem");
		$parser = $this->createMock("CaT\Ilse\Aux\Yaml");

		$this->object = new UpdatePluginsHelperForTest(
			$server,
			$plugins,
			$filesystem,
			$parser
			);
	}
	public function test_perform()
	{
	}
}