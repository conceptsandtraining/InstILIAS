<?php

use \InstILIAS\Config\Client;

class LanguageConfigTest extends PHPUnit_Framework_TestCase {
	public function test_not_enough_params() {
		try {
			$config = new Language();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	LanguageConfigValueProvider
	 */
	public function test_LanguageConfig($default_lang, array $install_lang, $valid) {
		if ($valid) {
			$this->_test_valid_LanguageConfig($default_lang, $install_lang,  $valid);
		}
		else {
			$this->_test_invalid_LanguageConfig($default_lang, $install_lang,$valid);
		}
	}

	public function _test_valid_LanguageConfig($default_lang, array $install_lang) {
		$config = new Language($default_lang, $install_lang);
		$this->assertEquals($default_lang, $config->defaultLang());
		$this->assertEquals($install_lang, $config->installLang());
	}

	public function _test_invalid_LanguageConfig($default_lang, array $install_lang) {
		try {
			$config = new Language($default_lang, $install_lang);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function LanguageConfigValueProvider() {
		$ret = array();
		foreach ($this->defaultLangProvider() as $default_lang) {
			foreach ($this->installLangsProvider() as $install_lang) {
				$ret[] = array
					( $default_lang[0], $install_lang[0]
					, $default_lang[1] && $install_lang[1]);
			}
		}
		return $ret;
	}

	public function defaultLangProvider() {
		return array(array("de", true)
					, array("en", true)
					, array("es", false)
					, array("da", false)
					, array("ar", false)
					, array("el", false)
				);
	}

	public function installLangsProvider() {
		return array(array(array("de","en"), true)
					, array(array("en","ar"), false)
					, array(array("es","sq"), false)
					, array(array("da","pl"), false)
					, array(array("ar","nl"), false)
					, array(array("el","sk"), false)
				);
	}
}
