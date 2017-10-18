<?php

/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

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

class UpdatePluginsHelperTest extends PHPUnit_Framework_TestCase
{
	protected $path = "test/dummy";
	protected $url = "https://my_plugin";
	protected $local_path = "/var/www/html/ilias";
	protected $server;
	protected $plugins;
	protected $filesystem;
	protected $parser;

	public function setUp()
	{
		$git = new Git($this->url, "master", "5355");
		$plugin = new Plugin($this->path, $git);

		$this->server = new Server("http://ilias.de", $this->local_path, "Europe/Berlin");
		$this->plugins = new Plugins($this->path, array($plugin));
		$this->filesystem = $this->createMock("CaT\Ilse\Aux\Filesystem");
		$this->parser = $this->createMock("CaT\Ilse\Aux\Yaml");

		$this->object = new UpdatePluginsHelperForTest(
			$this->server,
			$this->plugins,
			$this->filesystem,
			$this->parser
			);
	}

	public function test_getRepoUrls()
	{
		$this->assertContains($this->url, $this->object->getRepoUrls());
	}

	public function test_getRepoNameFromUrl()
	{
		$this->assertEquals($this->object->getRepoNameFromUrl($this->url), "my_plugin");
		$this->assertNotEquals($this->object->getRepoNameFromUrl("http://urlplugin_name"), "plugin_name");
	}

	public function test_getInstalledPlugins()
	{
		$this->filesystem
			->expects($this->once())
			->method("exists")
			->with($this->path)
			->willReturn(true);
		$this->filesystem
			->expects($this->once())
			->method("isEmpty")
			->with($this->path)
			->willReturn(false);
		$this->filesystem
			->expects($this->once())
			->method("getSubdirectories")
			->with($this->path)
			->willReturn(array("test_a", "test_b"));

		$result = $this->object->getInstalledPlugins($this->path);

		$this->assertContains("test_a", $result);
		$this->assertContains("test_b", $result);
	}

	public function test_getPluginLinkPath()
	{
		$common_path = "Customizing/global/plugins";
		$meta = [
			"ComponentType" => "Services",
			"ComponentName" => "EventHandling",
			"Slot" => "EventHook",
			"SlotId" => "evhk",
			"Name" => "Ilse"
			];
		$expected_path =
			$this->local_path."/".
			$common_path."/".
			$meta['ComponentType']."/".
			$meta['ComponentName']."/".
			$meta['Slot']
			;
		$this->filesystem
			->expects($this->once())
			->method("read");

		$this->parser
			->expects($this->once())
			->method("parse")
			->willReturn($meta);

		$result = $this->object->getPluginLinkPath("egal");

		$this->assertEquals($expected_path, $result['path']);
		$this->assertEquals($meta['Name'], $result['name']);
	}

	public function test_getUnlistedPlugins()
	{
		$installed = [
			"Apfel",
			"Birne",
			"Banane"
			];

		$listed = [
			"/Apfel",
			"/Birne",
			"/Erdbeere"
			];

		$result = $this->object->getUnlistedPlugins($installed, $listed);

		$this->assertContains("Banane", $result);
		$this->assertNotContains("Erdbeere", $result);
	}
}