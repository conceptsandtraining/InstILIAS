<?php

namespace CaT\Ilse\Aux\Git;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Wrapper for git commands
 *
 * TODO: make this expand ~ to home.
 * 
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 */
class GitWrapper implements Git
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $repo_url;

	/**
	 * @var bool
	 */
	protected $verbose;

	/**
	 * @var	string
	 */
	protected $remote;

	/**
	 * Constructor of the class GitWrapper
	 *
	 * TODO: IMO $repo_url in reality is $remote_location
	 *
	 * @param string    $path
	 * @param string    $repo_url
	 * @param string 	$name
	 * @param bool		$verbose
	 */
	public function __construct($path, $repo_url, $verbose = false)
	{
		assert('is_string($path)');
		assert('is_string($repo_url)');

		$this->path = $path;
		$this->repo_url = $repo_url;
		$this->verbose = $verbose;
		$this->remote = "origin";
	}

	/**
	 * @inheritdoc
	 */
	public function gitClone()
	{
		if ($this->repo_url == "")
		{
			throw new GitException("Unknown repo url!");
		}
		if($this->gitIsGitRepo())
		{
			throw new GitException($this->path." is already a git repo.");
		}

		$this->gitExec("git clone", array($this->repo_url, $this->path), "");
		$this->gitIgnoreFileModeChanges();

		return true;
	}

	/**
	 * Set ignore file mode permission change 
	 */
	public function gitIgnoreFileModeChanges()
	{
		$this->gitExec("git config core.fileMode false", array());
	}

	/**
	 * @inheritdoc
	 */
	public function gitFetch()
	{
		if(!$this->gitIsGitRepo())
		{
			throw new GitException("Fetch command on a non repo!");
		}
		$this->gitExec("git fetch", array($this->remote));
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gitPull($branch)
	{
		if(!$this->gitIsGitRepo())
		{
			throw new GitException("Pull command on a non repo!");
		}
		$this->gitExec("git pull", array($this->remote, $branch));
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gitCheckout($branch, $new = false)
	{
		assert('is_string($branch)');
		if($new && in_array($branch, $this->gitGetBranches()))
		{
			throw new GitException("Branch already exists!");
		}
		$args = array($branch);
		if($new)
		{
			array_unshift($args, "-b");
		}
		if(!$this->gitIsGitRepo())
		{
			throw new GitException("$this->path isn't a git repositrory");
		}
		$this->gitExec("git checkout", $args);

		return true;
	}

	/**
	 * Execute a git command
	 *
	 * @param string    	$cmd
	 * @param array     	$params
	 * @param bool			$use_tty
	 *
	 * @throws GitException
	 * @return string 
	 */
	protected function gitExec($cmd, array $params)
	{
		assert('is_string($cmd)');

		// remove spaces and avoid shell piping
		$clean = array_map(function ($i) {
				return escapeshellarg(trim($i));
			}, $params);

		$complete_command = $cmd." ".implode(' ', $clean);
		$working_dir = $this->path;

		$process = new Process($complete_command, $working_dir);
		$process->setTty($this->verbose);
		$process->run();
		$out = $process->getOutput();

		if(!$process->isSuccessful())
		{
			throw new GitException("Error when running `$complete_command`: $out"); 
		}
		return $out;
	}

	/**
	 * Get the repository path
	 *
	 * @return string
	 */
	public function gitGetPath()
	{
		return $this->path;
	}

	/**
	 * @inheritdoc
	 */
	public function gitGetBranches()
	{
		$out = $this->gitExec("git branch", array());
		return array_map('trim', explode("\n", trim(str_replace("*", " ", $out))));
	}
}
