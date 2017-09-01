<?php

use \CaT\Ilse\Config\Setup;

class SetupConfigTest extends PHPUnit_Framework_TestCase{
	public function test_not_enough_params() {
		try {
			$config = new Setup();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	SetupConfigValueProvider
	 */
	public function test_SetupConfig($master_password, $valid) {
		if ($valid) {
			$this->_test_valid_SetupConfig($master_password);
		}
		else {
			$this->_test_invalid_SetupConfig($master_password);
		}
	}

	public function _test_valid_SetupConfig($master_password) {
		$config = new Setup($master_password);
		$this->assertEquals($master_password, $config->masterPassword());
	}

	public function _test_invalid_SetupConfig($master_password) {
		try {
			$config = new Setup($master_password);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function SetupConfigValueProvider() {
		$ret = array();
		foreach ($this->masterPasswordProvider() as $master_password) {
			$ret[] = array
				( $master_password[0]
				, $master_password[1]);
		}
		return $ret;
	}

	public function masterPasswordProvider() {
		return array(
				array("pusteblume", true)
				, array(5, false)
				, array(true, false)
				, array(array(), false)
			);
	}
}
