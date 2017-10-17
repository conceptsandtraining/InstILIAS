<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

use \CaT\Ilse\Action\UpdatePluginsDirectory;
use \CaT\Ilse\Aux\UpdatePluginsHelper;
use \CaT\Ilse\Action\UpdatePlugins;
use \CaT\Ilse\Config;
use \CaT\Ilse\IliasReleaseConfigurator;
use \CaT\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\Git;
use CaT\Ilse\Aux;

use CaT\Ilse\Action\UpdatePlugin;

// If database had it own interface like filesystem, we could
// drop this and write a proper test instead.
class UpdatePluginsDirectoryForTest extends UpdatePluginsDirectory{
}

class UpdatePluginsDirectoryTest extends PHPUnit_Framework_TestCase
{
	public function test_perform()
	{
		$url = "https://my_plugin";
		$path = "test/dummy";
		$name = "my_plugin";
		$yaml = "---
ComponentType: Services
ComponentName: EventHandling
Slot: EventHook
SlotId: evhk";

		$git_factory = $this->createMock(Git\GitFactory::class);
		$git_wrapper = $this->createMock(Git\GitWrapper::class);
		$filesystem = $this->createMock("CaT\Ilse\Aux\Filesystem");
		$task_logger = $this->createMock(TaskLogger::class);
		$update_plugins_helper = $this->createMock(UpdatePluginsHelper::class);
		$update_plugins = $this->createMock(UpdatePlugins::class);
		$parser = $this->createMock("CaT\Ilse\Aux\Yaml");

		$git = new Config\Git($url, "master", "5355");
		$server = new Config\Server("http://ilias.de", "/var/www/html/ilias", "Europe/Berlin");
		$plugin = new Config\Plugin($path, $git);
		$plugins = new Config\Plugins($path, array($plugin));

		$action = new UpdatePluginsDirectoryForTest(
													$filesystem,
													$git_factory,
													$task_logger,
													$update_plugins_helper,
													$update_plugins,
													$parser);

		$filesystem
			->expects($this->any())
			->method("makeDirectory")
			->willReturn($path);
		$filesystem
			->expects($this->any())
			->method("exists")
			->willReturn(true);
		$filesystem
			->expects($this->any())
			->method("isWriteable")
			->willReturn(true);
		$filesystem
			->expects($this->any())
			->method("getSubdirectories")
			->willReturn(array("test", "test2"));
		$filesystem
			->expects($this->any())
			->method("read")
			->willReturn($yaml);

		$git_factory
			->expects($this->at(0))
			->method("getRepo")
			->with($path."/".$name, $url, false)
			->willReturn($git_wrapper);

		$task_logger
			->expects($this->any())
			->method("eventually")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));
		$task_logger
			->expects($this->any())
			->method("always")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));

		$git_wrapper
			->expects($this->any())
			->method("gitClone");

		$update_plugins_helper
			->expects($this->any())
			->method("getRepoUrls")
			->willReturn(array($url));
		$update_plugins_helper
			->expects($this->any())
			->method("getRepoNameFromUrl")
			->willReturn($name);
		$update_plugins_helper
			->expects($this->any())
			->method("getInstalledPlugins")
			->willReturn(array($name, $name));
		$update_plugins_helper
			->expects($this->any())
			->method("getUnlistedPlugins")
			->willReturn(array("test", "noch"));
		$update_plugins_helper
			->expects($this->any())
			->method("dir")
			->willReturn($path);

		$update_plugins
			->expects($this->any())
			->method("uninstall");

		$action->perform();
	}
}