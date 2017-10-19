<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

use \CaT\Ilse\Action\BuildInstallationEnvironment;
use \CaT\Ilse\Config;
use \CaT\Ilse\Aux\TaskLogger;
use \CaT\Ilse\Aux\Filesystem;
use \CaT\Ilse\Setup\RequirementsChecker;
use \CaT\Ilse\Aux\Git\Git;
use \CaT\Ilse\Aux\Git\GitFactory;

class BuildInstallationEnvironmentForTest extends BuildInstallationEnvironment {
}

class BuildInstallationEnvironmentTest extends PHPUnit_Framework_TestCase {
	public function test_perform() {
		$rc = $this->createMock(RequirementsChecker::class);
		$git = $this->createMock(Git::class);
		$git_factory = $this->createMock(GitFactory::class);
		$fs = $this->createMock(Filesystem::class);
		$db_config = new Config\DB("host", "database", "user", "password", "innodb", "utf8_general_ci", 1);
		$server_config = new Config\Server("http://path", "absolute_path", "Europe/Berlin");
		$client_config = new Config\Client("data_dir", "name", "bcrypt", 32);
		$log_config = new Config\Log("log_path", "log_filename", "error_log_path");
		$git_config = new Config\Git("http://git_repo", "branch_name", "");
		$task_logger = $this->createMock(TaskLogger::class);

		$action = new BuildInstallationEnvironment($server_config, $client_config, $db_config, $log_config, $git_config, $rc, $task_logger, $git_factory, $fs);

		$task_logger
			->expects($this->any())
			->method("eventually")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));
		$task_logger
			->expects($this->any())
			->method("always")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));
		$task_logger
			->expects($this->any())
			->method("progressing")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));

		$rc
			->expects($this->once())
			->method("webDirectoryExists")
			->with("absolute_path")
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("webDirectoryWriteable")
			->with("absolute_path")
			->willReturn(true);	
		$rc
			->expects($this->exactly(2))
			->method("webDirectoryContainsILIAS")
			->with("absolute_path")
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("dataDirectoryExists")
			->with("data_dir")
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("dataDirectoryWriteable")
			->with("data_dir")
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("dataDirectoryEmpty")
			->with("data_dir", "name")
			->willReturn(true);	
		$rc
			->expects($this->never())
			->method("pdoExist");
		$rc
			->expects($this->never())
			->method("databaseConnectable");
		$rc
			->expects($this->exactly(2))
			->method("logDirectoryExists")
			->withConsecutive(["log_path"],["error_log_path"])
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("logFileExists")
			->with("log_path/log_filename")
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("logFileWriteable")
			->with("log_path/log_filename")
			->willReturn(true);	
		$rc
			->expects($this->once())
			->method("logDirectoryWriteable")
			->with("error_log_path")
			->willReturn(true);

		$fs
			->expects($this->never())
			->method($this->anything());	

		$git_factory
			->expects($this->never())
			->method("getRepo");

		$action->perform();
	}

	public function test_perform_create_stuff() {
		$rc = $this->createMock(RequirementsChecker::class);
		$git = $this->createMock(Git::class);
		$git_factory = $this->createMock(GitFactory::class);
		$fs = $this->createMock(Filesystem::class);
		$db_config = new Config\DB("host", "database", "user", "password", "innodb", "utf8_general_ci", 1);
		$server_config = new Config\Server("http://path", "absolute_path", "Europe/Berlin");
		$client_config = new Config\Client("data_dir", "name", "bcrypt", 32);
		$log_config = new Config\Log("log_path", "log_filename", "error_log_path");
		$git_config = new Config\Git("http://git_repo", "branch_name", "");
		$task_logger = $this->createMock(TaskLogger::class);

		$action = new BuildInstallationEnvironment($server_config, $client_config, $db_config, $log_config, $git_config, $rc, $task_logger, $git_factory, $fs);

		$task_logger
			->expects($this->any())
			->method("eventually")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));
		$task_logger
			->expects($this->any())
			->method("always")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));
		$task_logger
			->expects($this->any())
			->method("progressing")
			->will($this->returnCallback(function($s, $c) {
				$c();
			}));

		$rc
			->expects($this->once())
			->method("webDirectoryExists")
			->with("absolute_path")
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("webDirectoryWriteable")
			->with("absolute_path")
			->willReturn(false);	
		$rc
			->expects($this->exactly(2))
			->method("webDirectoryContainsILIAS")
			->with("absolute_path")
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("dataDirectoryExists")
			->with("data_dir")
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("dataDirectoryWriteable")
			->with("data_dir")
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("dataDirectoryEmpty")
			->with("data_dir", "name")
			->willReturn(false);	
		$rc
			->expects($this->never())
			->method("pdoExist");
		$rc
			->expects($this->never())
			->method("databaseConnectable");
		$rc
			->expects($this->exactly(2))
			->method("logDirectoryExists")
			->withConsecutive(["log_path"],["error_log_path"])
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("logFileExists")
			->with("log_path/log_filename")
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("logFileWriteable")
			->with("log_path/log_filename")
			->willReturn(false);	
		$rc
			->expects($this->once())
			->method("logDirectoryWriteable")
			->with("error_log_path")
			->willReturn(false);

		$fs
			->expects($this->exactly(4))
			->method("makeDirectory")
			->withConsecutive(["absolute_path"],["data_dir"],["log_path"],["error_log_path"]);

		$fs->expects($this->exactly(4))
			->method("chmod")
			->withConsecutive(["absolute_path", 0755],["data_dir", 0755],["log_path/log_filename", 0755],["error_log_path", 0755]);

		$fs->expects($this->once())
			->method("write")
			->with("log_path/log_filename", "");

		$fs->expects($this->exactly(2))
			->method("purgeDirectory")
			->withConsecutive(["absolute_path"],["data_dir"]);

		$git_factory
			->expects($this->once())
			->method("getRepo")
			->with("absolute_path", "http://git_repo", "ILIAS")
			->willReturn($git);

		$git
			->expects($this->once())
			->method("gitClone");
		$git
			->expects($this->once())
			->method("gitCheckout")
			->with("branch_name");

		$action->perform();
	}

}
