<?php

use \CaT\Ilse\Config\Log;

class LogConfigTest extends PHPUnit_Framework_TestCase{
	public function test_not_enough_params() {
		try {
			$config = new Log();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	LogConfigValueProvider status
	 */
	public function test_LogConfig($path, $fileName, $errorLog, $valid) {
		if ($valid) {
			$this->_test_valid_LogConfig($path, $fileName, $errorLog);
		}
		else {
			$this->_test_invalid_LogConfig($path, $fileName, $errorLog);
		}
	}

	public function _test_valid_LogConfig($path, $fileName, $errorLog) {
		$config = new Log($path, $fileName, $errorLog);
		$this->assertEquals($path, $config->path());
		$this->assertEquals($fileName, $config->fileName());
	}

	public function _test_invalid_LogConfig($path, $fileName, $errorLog) {
		try {
			$config = new Log($path, $fileName, $errorLog);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function LogConfigValueProvider() {
		$ret = array();
		$take_it = 0;
		$take_every_Xth = 10;
		foreach ($this->pathProvider() as $path) {
			foreach ($this->fileNameProvider() as $fileName) {
				foreach($this->errorLogProvider() as $errorLog) {
					$ret[] = array
						( $path[0], $fileName[0] , $errorLog[0]
						, $path[1] && $fileName[1] && $errorLog[1]);
				}
			}
		}
		return $ret;
	}

	public function fileNameProvider() {
		return array(
				array("ilias.log", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function pathProvider() {
		return array(
				array("/path", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function errorLogProvider() {
		return array(
				array("/path", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}
}
