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
class ReinstallCommand extends BaseCommand
{
	/**
	 * Configure the command with description and help text
	 */
	protected function configure()
	{
		$this
			->setName("update")
			->setDescription("Reinstall the Ilias-Environment.")
			->addArgument("config_name", InputArgument::REQUIRED, "Name of the Ilias Config File.")
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
		$args = ["config_name" => $in->getArgument("config_name")
				];

		$this->start($args);
		$this->updateConfig($args);
		$out->writeln("\t\t\t\tDone!");
	}

	/**
	 * Start the installation process
	 *
	 * @param ["param_name" => param_value] 	$args
	 */
	protected function start(array $args)
	{
		$this->process->setWorkingDirectory($this->path->getCWD() . "/" . "src/bin");
		$this->process->setCommandLine("php update_ilias.php "
									   . $this->getConfigPathByName($args['config_name'])
									  );
		$this->process->setTty(true);
		$this->process->run();
	}

	/**
	 * Start the update configuration process of ILIAS
	 *
	 * @param ["param_name" => param_value] 	$args
	 */
	protected function updateConfig(array $args)
	{
		$this->process->setWorkingDirectory($this->path->getCWD() . "/" . "src/bin");
		$this->process->setCommandLine("php update_configuration_ilias.php "
									   . $this->getConfigPathByName($args['config_name'])
									  );
		$this->process->setTty(true);
		$this->process->run();
	}
}