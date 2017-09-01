<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

use \CaT\Ilse\Command\ExampleConfigCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExampleConfigCommandForTest extends ExampleConfigCommand {
	public function _execute(InputInterface $in, OutputInterface $out) {
		return $this->execute($in, $out);
	}
}

class ExampleConfigTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->command = new ExampleConfigCommandForTest();
	}

	public function test_execute() {
		$in = $this->createMock(InputInterface::class);
		$out = $this->createMock(OutputInterface::class);

		$expected = file_get_contents(__DIR__."/../assets/example_config.yaml");

		$out
			->expects($this->once())
			->method("write")
			->with($expected);

		$this->command->_execute($in, $out);
	}
}
