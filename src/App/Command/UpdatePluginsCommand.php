<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use CaT\Ilse\Action;

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
		$this->dic["aux.taskLogger"] = $this->buildTaskLogger($out);

		$init_app_folder = $this->dic["action.initAppFolder"];
		$init_app_folder->perform();

		$config_names = $in->getArgument("config_names");
		$this->dic = $this->dic["aux.configLoader"]->loadConfigToDic($this->dic, $config_names);

		$update_plugins_directory = $this->dic["action.updatePluginsDirectory"];
		$update_plugins_directory->perform();

		// $update_plugins = $this->dic["action.updatePlugins"];
		// $update_plugins->perform();
	}
}
