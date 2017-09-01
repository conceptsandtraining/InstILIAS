<?php

use \CaT\Ilse\Config\HTTPSAutoDetect;

class HTTPSAutoDetectConfigTest extends PHPUnit_Framework_TestCase{
	public function test_not_enough_params() {
		try {
			$config = new HTTPSAutoDetect();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	HTTPSAutoDetectValueProvider
	 */
	public function test_HTTPSAutoDetectConfig($enabled, $header_name, $header_value, $valid) {
		if ($valid) {
			$this->_test_valid_HTTPSAutoDetectConfig($enabled, $header_name, $header_value);
		}
		else {
			$this->_test_invalid_HTTPSAutoDetectConfig($enabled, $header_name, $header_value);
		}
	}

	public function _test_valid_HTTPSAutoDetectConfig($enabled, $header_name, $header_value) {
		$config = new HTTPSAutoDetect($enabled, $header_name, $header_value);
		$this->assertEquals($enabled, $config->enabled());
		$this->assertEquals($header_name, $config->headerName());
		$this->assertEquals($header_value, $config->headerValue());
	}

	public function _test_invalid_HTTPSAutoDetectConfig($enabled, $header_name, $header_value) {
		try {
			$config = new HTTPSAutoDetect($enabled, $header_name, $header_value);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function HTTPSAutoDetectValueProvider() {
		$ret = array();
		foreach ($this->enabledProvider() as $enabled) {
			$ret[] = array
				( $enabled[0], "", ""
				, $enabled[1]);
		}

		return $ret;
	}

	public function enabledProvider() {
		return array(
				array(0, true)
				, array(1, true)
				, array(4, false)
				, array(false, false)
				, array(array(), false)
				, array("https://localhost/", false)
			);
	}
}
