<?php

use \CaT\Ilse\Config\Server;

class ServerConfigTest extends PHPUnit_Framework_TestCase{
	public function test_not_enough_params() {
		try {
			$config = new Server();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	ServerConfigValueProvider
	 */
	public function test_ServerConfig($http_path, $absolute_path, $timezone, $valid) {
		if ($valid) {
			$this->_test_valid_ServerConfig($http_path, $absolute_path, $timezone);
		}
		else {
			$this->_test_invalid_ServerConfig($http_path, $absolute_path, $timezone);
		}
	}

	public function _test_valid_ServerConfig($http_path, $absolute_path, $timezone) {
		$config = new Server($http_path, $absolute_path, $timezone);
		$this->assertEquals($http_path, $config->httpPath());
		$this->assertEquals($absolute_path, $config->absolutePath());
		$this->assertEquals($timezone, $config->timezone());
	}

	public function _test_invalid_ServerConfig($http_path, $absolute_path, $timezone) {
		try {
			$config = new Server($http_path, $absolute_path, $timezone);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function ServerConfigValueProvider() {
		$ret = array();
		foreach ($this->HTTPPathProvider() as $http_path) {
			foreach ($this->absolutePathProvider() as $absolute_path) {
				foreach ($this->timezoneProvider() as $timezone) {
					$ret[] = array
						( $http_path[0], $absolute_path[0], $timezone[0]
						, $http_path[1] && $absolute_path[1] && $timezone[1]);
				}
			}
		}
		return $ret;
	}

	public function HTTPPathProvider() {
		return array(
				array("http://localhost/", true)
				, array("htt://localhost/", false)
				, array(4, false)
				, array(false, false)
				, array(array(), false)
				, array("https://localhost/", true)
			);
	}

	public function absolutePathProvider() {
		return array(
				array("/path", true)
				, array(4, false)
				, array(false, false)
				, array(array(), false)
			);
	}

	public function timezoneProvider() {
		return array(
				array("Europe/Berlin", true)
				, array(4, false)
				, array("Europe", false)
				, array("Europe/Bern", true)
				, array(false, false)
				, array(array(), false)
			);
	}
}
