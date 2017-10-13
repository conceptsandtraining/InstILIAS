<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

use \CaT\Ilse\Action\UpdatePluginsDirectory;
use \CaT\Ilse\Action\UpdatePlugins;
use \CaT\Ilse\Config;
use \CaT\Ilse\IliasReleaseConfigurator;
use \CaT\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\Git;
use CaT\Ilse\Aux;

// If database had it own interface like filesystem, we could
// drop this and write a proper test instead.
class UpdatePluginsDirectoryForTest extends UpdatePluginsDirectory{
	public function _getUnlistedPlugins($installed, $listed)
	{
		return $this->getUnlistedPlugins($installed, $listed);
	}

	public function _getRepoNameFromUrl($url)
	{
		return $this->getRepoNameFromUrl($url);
	}
}

class UpdatePluginsDirectoryTest extends PHPUnit_Framework_TestCase
{
	public function test_perform()
	{
		$url = "https://my_plugin";
		$path = "test/dummy";
		$name = "my_plugin";

		$git_factory 	= $this->createMock(Git\GitFactory::class);
		$git_wrapper 	= $this->createMock(Git\GitWrapper::class);
		$filesystem 	= $this->createMock("CaT\Ilse\Aux\Filesystem");
		$task_logger 	= $this->createMock(TaskLogger::class);
		$update_plugins = $this->createMock(UpdatePlugins::class);

		$git 			= new Config\Git($url, "master", "5355");
		$server 		= new Config\Server("http://ilias.de", "/var/www/html/ilias", "Europe/Berlin");
		$plugin 		= new Config\Plugin($path, $git);
		$plugins 		= new Config\Plugins($path, array($plugin));

		$action 		= new UpdatePluginsDirectoryForTest($server, $plugins, $filesystem, $git_factory, $task_logger, $update_plugins);

		$filesystem
			->expects($this->any())
			->method("makeDirectory")
			->willReturn($path);
		$filesystem
			->expects($this->any())
			->method("exists")
			->willReturn(true);
		$filesystem
			->expects($this->once())
			->method("getSubdirectories")
			->willReturn(array("test", "test2"));

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

		$git_factory
			->expects($this->at(0))
			->method("getRepo")
			->with($path."/".$name, $url, false)
			->willReturn($git_wrapper);
		$git_factory
			->expects($this->at(1))
			->method("getRepo")
			->with($path."/".$name, $url, false)
			->willReturn($git_wrapper);

		$git_wrapper
			->expects($this->any())
			->method("gitClone");

		$update_plugins
			->expects($this->any())
			->method("uninstall");

		$action->perform();
	}

	public function test_getUnlistedPlugins()
	{
		$url = "https://my_plugin";
		$path = "test/dummy";
		$name = "my_plugin";

		$git_factory 	= $this->createMock(Git\GitFactory::class);
		$git_wrapper 	= $this->createMock(Git\GitWrapper::class);
		$filesystem 	= $this->createMock("CaT\Ilse\Aux\Filesystem");
		$task_logger 	= $this->createMock(TaskLogger::class);
		$update_plugins = $this->createMock(UpdatePlugins::class);

		$git 			= new Config\Git($url, "master", "5355");
		$server 		= new Config\Server("http://ilias.de", "/var/www/html/ilias", "Europe/Berlin");
		$plugin 		= new Config\Plugin($path, $git);
		$plugins 		= new Config\Plugins($path, array($plugin));

		$this->action 	= new UpdatePluginsDirectoryForTest($server, $plugins, $filesystem, $git_factory, $task_logger, $update_plugins);

		$installed = ['ilias-plugin-Accounting', 'ilias-plugin-Venues', 'ilias-plugin-MaterialList'];
		$listed = ['https://github.com/conceptsandtraining/ilias-plugin-Accounting',
				   'https://github.com/conceptsandtraining/ilias-plugin-Venues'];

		$uninstall = $this->action->_getUnlistedPlugins($installed, $listed);

		$this->assertTrue(array_shift($uninstall) == "ilias-plugin-MaterialList");
	}
}