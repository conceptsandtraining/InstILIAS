<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Implementation of the delete command
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
class DeleteCommand extends BaseCommand
{
	/**
	 * Configure the command with description and help text
	 */
	protected function configure()
	{
		$this
			->setName("delete")
			->setDescription("Delete the Ilias-Environment.")
			->addArgument("config_names", InputArgument::IS_ARRAY, "Name of the Ilias Config Files.")
			->addOption("all", "a", InputOption::VALUE_NONE, "Also delete log files and the data folder.")
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
		$config_names = $in->getArgument("config_names");
		$args["config"] = $this->merge($config_names);
		$args["all"] = $in->getOption("all");

		$this->delete($args);
		$out->writeln("\t\t\t\tDone!");
	}
}