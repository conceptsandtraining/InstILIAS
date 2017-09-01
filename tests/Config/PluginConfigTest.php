<?php

use \CaT\Ilse\Config\Plugin;

class PluginConfigTest extends PHPUnit_Framework_TestCase {
	public function test_not_enough_params() {
		try {
			$config = new Plugin();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	PluginConfigValueProvider
	 */
	public function test_PluginConfig($name, $git, $valid) {
		if ($valid) {
			$this->_test_valid_PluginConfig($name, $git);
		}
		else {
			$this->_test_invalid_PluginConfig($name, $git);
		}
	}

	public function _test_valid_PluginConfig($name, $git) {
		$config = new Plugin($name, $git);
		$this->assertEquals($name, $config->name());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Git", $config->git());
	}

	public function _test_invalid_PluginConfig($name, $git) {
		try {
			$config = new Plugin($name, $git);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function PluginConfigValueProvider() {
		$ret = array();
		foreach ($this->nameProvider() as $name) {
			foreach ($this->gitProvider() as $git) {
				$ret[] = array
					( $name[0], $git[0]
					, $name[1] && $git[1]);
				}
			}

		return $ret;
	}

	public function gitProvider() {
		return array(array(new \CaT\Ilse\Config\Git("url", "branch", ""), true)
					, array(4, false)
					, array(true, false)
					, array(array(), false)
				);
	}

	public function nameProvider() {
		return array(array("pluginname", true)
					, array(4, false)
					, array(true, false)
					, array(array(), false)
				);
	}
}
