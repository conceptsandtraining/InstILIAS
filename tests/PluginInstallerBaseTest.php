<?php

use \CaT\Ilse\Config\Plugins;
use \CaT\Ilse\Aux\YamlConfigParser;

require_once(__DIR__."/mocks/IliasPluginInstallerMock.php");



class PluginInstallerBaseTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->parser = new YamlConfigParser();
		$this->yaml_string = "--- 
dir: /home/test
plugins:
    0:
        component_type: Services
        component_name: Cron
        plugin_slot: CronHook
        name: Pluginname
        git:
            url: Bernd
            branch: master";

        $this->config = $this->parser->read_config($this->yaml_string, "\\CaT\\Ilse\\Config\\Plugins");
        $this->plugin_installer = new IliasPluginInstallerMock();
	}

	public function test_installPlugin() {
		$val = $this->config->plugins();
		$result = $this->plugin_installer->install($val[0], "pfad_zu_ilias");
		$this->assertTrue($result);
	}

	public function test_updatePlugin() {
		$val = $this->config->plugins();
		$result = $this->plugin_installer->update($val[0]);
		$this->assertTrue($result);
	}

	public function test_activatePlugin() {
		$val = $this->config->plugins();
		$result = $this->plugin_installer->activate($val[0]);
		$this->assertTrue($result);
	}

	public function test_deactivatePlugin() {
		$val = $this->config->plugins();
		$result = $this->plugin_installer->deactivate($val[0]);
		$this->assertTrue($result);
	}
}
