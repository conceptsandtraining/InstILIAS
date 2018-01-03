<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

use CaT\Ilse\Config;
use CaT\Ilse\Aux\ILIAS;
use CaT\Ilse\Aux\TaskLogger;
use \CaT\Ilse\Aux\YamlConfigParser;
use \CaT\Ilse\Aux\ILIAS\PluginInfo;
use \CaT\Ilse\Action\UpdatePlugins;
use \CaT\Ilse\Setup\PluginAdministrationFactory;
use \CaT\Ilse\Setup\PluginAdministration52;

// If database had it own interface like filesystem, we could
// drop this and write a proper test instead.
class UpdatePluginsForTest extends UpdatePlugins {

}

class UpdatePluginsTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
		$this->parser = new YamlConfigParser();
		$this->yaml_string = "---
client:
    data_dir: /data_dir
    name: Ilias5
    password_encoder: bcrypt
    session_expire: 120
database:
    host: 127.0.0.1
    database: ilias
    user: user
    password: passwd
    engine: innodb
    encoding: utf8_general_ci
    create_db: 1
language:
    default: de
    available:
        - en
        - de
server:
    http_path: http://localhost/
    absolute_path: /yourpath
    timezone: Europe/Berlin
setup:
    master_password: passwd
tools:
    convert: /convert
    zip: /zip
    unzip: /unzip
    java: /java
log:
    path: /path
    file_name: ilias.log
    error_log: /path
git:
    url: https://github.com/ILIAS-eLearning/ILIAS.git
    branch: release_5-1
https_auto_detect:
    enabled: 0
    header_name: X-FORWARDED-SCHEME
    header_value: https
plugin:
    plugins: []
    dir: bla
";
	}

	public function test_perform()
	{
		$url = "https://-my_plugin";
		$path = "/home/vagrant/dummy";
		$name = "my_plugin";

		$config = $this->parser->read_config($this->yaml_string, "\\CaT\\Ilse\\Config\\General");
		$plugin_admin_factory = $this->createMock(PluginAdministrationFactory::class);
		$task_logger = $this->createMock(TaskLogger::class);
		$plugin_admin = $this->createMock(PluginAdministration52::class);
		$filesystem = $this->createMock("CaT\Ilse\Aux\Filesystem");
		$plugin_info_reader_factory = $this->createMock(ILIAS\PluginInfoReaderFactory::class);

		$git = new Config\Git($url, "master", "5355");
		$server = new Config\Server("http://ilias.de", "/var/www/html/ilias", "Europe/Berlin");
		$plugin = new Config\Plugin($path, $git);
		$plugins = new Config\Plugins($path, array($plugin));
		$info = new PluginInfo("Service", "Repository", "RepositoryObject", "robj", $name);
		$action = new UpdatePluginsForTest(
			$server,
			$plugins,
			$filesystem,
			$config,
			$plugin_admin_factory,
			$task_logger,
			$plugin_info_reader_factory
		);

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

		$plugin_admin_factory
			->expects($this->once())
			->method("getPluginAdministrationForRelease")
			->with("5.2", $config, $task_logger)
			->willReturn($plugin_admin);

		$plugin_admin
			->expects($this->once())
			->method("needsUpdate")
			->willReturn(true);
		$plugin_admin
			->expects($this->once())
			->method("update")
			->with($name);
		$plugin_admin
			->expects($this->once())
			->method("activate")
			->with($name);
		$plugin_admin
			->expects($this->once())
			->method("updateLanguage")
			->with($name);

		$action->perform();
	}
}
