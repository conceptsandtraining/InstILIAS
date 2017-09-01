<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Executer;

use CaT\Ilse\App;

/**
 * Base class for all executers
 */
abstract class BaseExecuter
{
	/**
	 * @var string
	 */
	protected $gc;

	/**
	 * @var \CaT\Ilse\Interfaces\RequirementChecker
	 */
	protected $checker;

	/**
	 * @var \CaT\Ilse\Interfaces\Git
	 */
	protected $git;

	/**
	 * @var string
	 */
	protected $http_path;

	/**
	 * @var string
	 */
	protected $absolute_path;

	/**
	 * @var string
	 */
	protected $data_path;

	/**
	 * @var string
	 */
	protected $client_id;

	/**
	 * @var string
	 */
	protected $git_url;

	/**
	 * @var string
	 */
	protected $git_branch_name;

	/**
	 * @var string
	 */
	protected $web_dir;

	/**
	 * @var string
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $group;

	/**
	 * Constructor of the BaseExecuter class
	 *
	 * @param string 									$config
	 * @param \CaT\Ilse\Interfaces\RequirementChecker 	$checker
	 * @param \CaT\Ilse\Interfaces\Git 					$git
	 * @param \CaT\Ilse\Interfaces\Pathes 		$path
	 */
	public function __construct($config,
								\CaT\Ilse\Interfaces\RequirementChecker $checker,
								\CaT\Ilse\Interfaces\Git $git,
								\CaT\Ilse\Interfaces\Pathes $path)
	{
		assert('is_string($config)');

		$parser = new \CaT\Ilse\YamlParser();
		$this->gc = $parser->read_config($config, "\\CaT\\Ilse\\Config\\General");

		$this->checker 			= $checker;
		$this->git 				= $git;
		$this->user 			= $this->gc->setup()->user();
		$this->group 			= $this->gc->setup()->group();
		$this->http_path 		= $path->expandHomeFolder($this->gc->server()->httpPath());
		$this->absolute_path 	= $path->expandHomeFolder($this->gc->server()->absolutePath());
		$this->data_path 		= $path->expandHomeFolder($this->gc->client()->dataDir());
		$this->client_id 		= $path->expandHomeFolder($this->gc->client()->name());
		$this->git_url 			= $path->expandHomeFolder($this->gc->git()->url());
		$this->git_branch_name 	= $path->expandHomeFolder($this->gc->git()->branch());
		$this->error_log 		= $path->expandHomeFolder($this->gc->log()->errorLog());
		$this->web_dir 			= $path->expandHomeFolder(App::I_D_WEB_DIR);
	}
}