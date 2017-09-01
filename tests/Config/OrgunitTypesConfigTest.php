<?php

use \CaT\Ilse\Config\OrgunitTypes;
use \CaT\Ilse\YamlParser;

class OrgunitTypesConfigTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->parser = new YamlParser();
		$this->yaml_string = "--- 
orgunit_types:
    0:
        default_language: &ORGU1_TYPE_DEFAULT_LANGUAGE de
        type_language_settings:
            0:
                language: de
                title: &ORGU1_TYPE OrgunitTypeTest
                description: Ich bin ein Test";
	}

	public function test_not_enough_params() {
		try {
			$config = new OrgunitTypes();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function test_orgunitTypesConfig() {
		$config = $this->parser->read_config($this->yaml_string, "\\CaT\\Ilse\\Config\\OrgunitTypes");

		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\OrgunitTypes", $config);
	}
}