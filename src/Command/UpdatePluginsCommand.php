<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use CaT\Ilse\Executor;

/**
 * Implementation of the updateplugins command
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
class UpdatePluginsCommand extends BaseCommand
{
/**
	 * Configure the command with description and help text
	 */
	protected function configure()
	{
		$this
			->setName("updateplugins")
			->setDescription("Update the plugins of the Ilias-Environment.")
			->addArgument("config_names", InputArgument::IS_ARRAY, "Name of the Ilias Config Files.")
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
		$args["config"] = $this->merge($config_names);

		$this->update($args);
		$out->writeln("\t\t\t\tDone!");
	}

	/**
	 * Start the update configuration process of ILIAS
	 *
	 * @param ["param_name" => param_value] 	$args
	 */
	protected function update(array $args)
	{
		$u = new Executor\UpdatePluginsILIAS($args['config'], $this->checker, $this->git);
		$u->run();
	}
}