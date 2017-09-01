<?php

use \CaT\Ilse\Config\OrgUnits;
use \CaT\Ilse\YamlParser;

class OrgUnitsConfigTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->parser = new YamlParser();
		$this->yaml_string = "--- 
orgunits:
    0:
        title: Eins
    1:
        title: Zwei
        children:
            10:
                title: ZweiEins
                children: []
            11:
                title: ZweiZwei
                children: []
    2:
        title: Drei
        children: []";
	}

	public function test_not_enough_params() {
		try {
			$config = new OrgUnits();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function test_createOrgUnitsConfig() {
		$config = $this->parser->read_config($this->yaml_string, "\\CaT\\Ilse\\Config\\OrgUnits");

		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\OrgUnits", $config);
	}
}