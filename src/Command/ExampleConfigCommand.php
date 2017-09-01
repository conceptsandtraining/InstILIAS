<?php
/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Implementation of the example config command
 *
 * @author Richard Klees <richard.klees@concepts-and-training.de>
 */
class ExampleConfigCommand extends Command
{
	/**
	 * Configure the command with description and help text
	 */
	protected function configure()
	{
		$this
			->setName("example-config")
			->setDescription("Dumps an example configuration containing all possible fields for configuration.")
			;
	}

	/**
	 * Executes the command
	 *
	 * @param InputInterface 	$in
	 * @param OutputInterface 	$out
	 */
	protected function execute(InputInterface $in, OutputInterface $out)
	{
		$this->dumpExampleConfig($out);
	}

	/**
	 * Dump the example config.
	 *
	 * @param OutputInterface	$out
	 */
	protected function dumpExampleConfig(OutputInterface $out)
	{
		$out->write(file_get_contents(__DIR__."/../../assets/example_config.yaml"));
	}
}
