<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use CaT\Ilse\Executor;

/**
 * Implementation of the reinstall command
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
class ReinstallCommand extends BaseCommand
{
	/**
	 * Configure the command with description and help text
	 */
	protected function configure()
	{
		$this
			->setName("reinstall")
			->setDescription("Reinstall the Ilias-Environment.")
			->addArgument("config_names", InputArgument::IS_ARRAY, "Name of the Ilias Config Files.")
			->addOption("interactive", "i", InputOption::VALUE_NONE, "Set i to start the setup in interactiv mode.")
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
		$args["interactive"] = $in->getOption("interactive");
		$args["all"] = $in->getOption("all");

		$this->delete($args);
		$this->setup($args);
		$this->start($args);
		$this->config('./ilse.php config ' . implode(" ", $config_names));
		$out->writeln("\t\t\t\tDone!");
	}
}