<?php

use \CaT\Ilse\Config\Tools;

class ToolConfigTest extends PHPUnit_Framework_TestCase{
	public function test_not_enough_params() {
		try {
			$config = new Tools();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	ToolsConfigValueProvider
	 */
	public function test_ToolsConfig($convert, $zip, $unzip, $java, $valid) {
		if ($valid) {
			$this->_test_valid_ToolsConfig($convert, $zip, $unzip, $java);
		}
		else {
			$this->_test_invalid_ToolsConfig($convert, $zip, $unzip, $java);
		}
	}

	public function _test_valid_ToolsConfig($convert, $zip, $unzip, $java) {
		$config = new Tools($convert, $zip, $unzip, $java);
		$this->assertEquals($convert, $config->convert());
		$this->assertEquals($zip, $config->zip());
		$this->assertEquals($unzip, $config->unzip());
		$this->assertEquals($java, $config->java());
	}

	public function _test_invalid_ToolsConfig($convert, $zip, $unzip, $java) {
		try {
			$config = new Tools($convert, $zip, $unzip, $java);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function ToolsConfigValueProvider() {
		$ret = array();
		foreach ($this->convertProvider() as $convert) {
			foreach ($this->zipProvider() as $zip) {
				foreach ($this->unzipProvider() as $unzip) {
					foreach ($this->javaProvider() as $java) {
						$ret[] = array
							( $convert[0], $zip[0], $unzip[0], $java[0]
							, $convert[1] && $zip[1] && $unzip[1] && $java[1]);
					}
				}
			}
		}
		return $ret;
	}

	public function convertProvider() {
		return array(
				array("/ImageMagick", true)
				, array(5, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function zipProvider() {
		return array(
				array(5, false)
				, array(true, false)
				, array("/zip", true)
				, array(array(), false)
			);
	}

	public function unzipProvider() {
		return array(
				array(5, false)
				, array("/nzip", true)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function javaProvider() {
		return array(
				array(5, false)
				, array(true, false)
				, array(array(), false)
				, array("/java", true)
			);
	}
}
