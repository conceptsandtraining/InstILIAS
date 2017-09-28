<?php

/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

use \CaT\Ilse\App\Command\UpdatePluginsCommand;
use \CaT\Ilse\Aux\TaskLogger;
use \CaT\Ilse\Aux\ConfigLoader;
use \CaT\Ilse\Action;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePluginsCommandForTest extends UpdatePluginsCommand {
	public $task_logger = null;
	public function buildTaskLogger() {
		return $this->task_logger;
	}
	public function _execute(InputInterface $in, OutputInterface $out) {
		return $this->execute($in, $out);
	}
}

class UpdatePluginsCommandTest extends PHPUnit_Framework_TestCase {

	public function test_execute() {
		$configs = ["CONFIG"];

		$out = $this->createMock(OutputInterface::class);

		$in = $this->createMock(InputInterface::class);
		$in
			->expects($this->once())
			->method("getArgument")
			->with("config_names")
			->willReturn($configs);

		$init_app_folder = $this->createMock(Action\InitAppFolder::class);
		$init_app_folder
			->expects($this->once())
			->method("perform");

		$update_plugins = $this->createMock(Action\UpdatePlugins::class);
		$update_plugins
			->expects($this->once())
			->method("perform");

		$update_plugins_directory = $this->createMock(Action\UpdatePluginsDirectory::class);
		$update_plugins_directory
			->expects($this->once())
			->method("perform");

		$config_loader = $this->createMock(ConfigLoader::class);

		$dic["action.initAppFolder"] 			= $init_app_folder;
		$dic["action.updatePlugins"] 			= $update_plugins;
		$dic["aux.configLoader"] 				= $config_loader;
		$dic["action.updatePluginsDirectory"] 	= $update_plugins_directory;

		$config_loader
			->expects($this->once())
			->method("loadConfigToDic")
			->with($this->anything(), $configs)
			->willReturn($dic);

		$command = new UpdatePluginsCommandForTest($dic);
		$command->_execute($in, $out);
	}
}
