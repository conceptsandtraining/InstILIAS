<?php

/**
 * Test class for git commands
 * 
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 */
class MergeConfigsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Setup the testing environment
	 */
	public function setUp()
	{
		$this->merger = new CaT\Ilse\MergeConfigs();
		$this->yaml_1 = "---
client:
    data_dir: /home/dw/testing
    name: test_name
    data: /here/itis";

    	$this->yaml_2 = "---
client:
    data_dir: /home/dw/logging
    password: abcdef
    user: root";

    	$this->yaml_3 = "---
client:
    name: myname
server:
    path: /var/www/html/
    user: www-data";

	}

	public function test_mergeConfigs()
	{
		$arr = $this->merger->mergeConfigs(array($this->yaml_1, $this->yaml_2, $this->yaml_3));
		$expected = "client:
    data_dir: /home/dw/logging
    name: myname
    data: /here/itis
    password: abcdef
    user: root
server:
    path: /var/www/html/
    user: www-data
";
		$this->assertEquals($expected, $arr);
	}
}
