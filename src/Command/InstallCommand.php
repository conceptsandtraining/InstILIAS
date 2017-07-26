<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Implementation of the install command
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
class InstallCommand extends BaseCommand
{
	/**
	 * Configure the command with description and help text
	 */
	protected function configure()
	{
		$this
			->setName("install")
			->setDescription("Start the installation.")
			->addArgument("config_names", InputArgument::IS_ARRAY, "Name of the Ilias Config Files.")
			->addOption("interactive", "i", InputOption::VALUE_NONE, "Set i to start the setup with interatcion.");
			;
	}

	/**
	 * Exexutes the command
	 *
	 * @param InputInterface 	$in
	 * @param OutputInterface 	$out
	 */
	protected function execute(InputInterface $in, OutputInterface $out)
	{
		$config_names = $in->getArgument("config_names");
		$args = ["config" => $this->merge($config_names),
				 "interactive" => $in->getOption("interactive")];
		$this->start($args);
		$this->config($args);
		$out->writeln("\t\t\t\tDone!");
	}

	/**
	 * Start the installation process
	 *
	 * @param ["param_name" => param_value] 	$args
	 */
	protected function start(array $args)
	{
		$this->process->setWorkingDirectory($this->path->getCWD() . "/src/bin");
		$this->process->setCommandLine("php install_ilias.php "
									 . $args['config']
									 . " non_interactiv");
		$this->process->setTty(true);
		$this->process->run();
	}

	/**
	 * Start the configuration process of ILIAS
	 *
	 * @param ["param_name" => param_value] 	$args
	 */
	protected function config(array $args)
	{
		$this->process->setWorkingDirectory($this->path->getCWD() . "/" . "src/bin");
		$this->process->setCommandLine("php configurate_ilias.php "
									  . $this->getConfigPathByName($args['config_name'])
									  );
		$this->process->setTty(true);
		$this->process->run();
	}
}