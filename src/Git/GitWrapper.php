<?php

namespace CaT\Ilse\Git;

use DWLibrary\Git\GitException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Wrapper for git commands
 * 
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 */
class GitWrapper implements Git
{
	/**
	 * @var string
	 */
	protected $cwd;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $repo_url;

	/**
	 * @var string
	 */
	protected $repo_name;

	/**
	 * @var Symfony\Component\Process\Process
	 */
	protected $process;

	/**
	 * Constructor of the class GitWrapper
	 *
	 * @param string    $path
	 * @param string    $repo_url
	 */
	public function __construct($path, $repo_url)
	{
		assert('is_string($path)');
		assert('is_string($repo_url)');

		$this->path = $path;
		$this->repo_url = $repo_url;
		$this->repo_name = $this->gitGetName($repo_url);
		$this->process = new Process("");
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
			throw new GitException($this->path.'/'.$this->repo_name." is already a git repo.");
		}
		try
		{
			$this->gitExec("git clone", array($this->repo_url), $this->repo_name);
		}
		catch(GitException $e)
		{
			echo($e->__toString());
		}
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gitPull($remote="origin", $branch="master")
	{
		if(!$this->gitIsGitRepo())
		{
			throw new GitException("Pull command on a non repo!");
		}
		try
		{
			$this->gitExec("git pull", array($remote, $branch, $this->repo_name));
		}
		catch(GitException $e)
		{
			echo($e->__toString());
		}
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function gitCheckout($branch, $new)
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
		try
		{
			$this->gitExec("git checkout", $args, $this->repo_name);
		}
		catch(GitException $e)
		{
			echo($e->__toString());
		}
		return true;
	}

	/**
	 * 
	 */
	public function gitIsGitRepo()
	{
		return is_dir($this->path.'/'.$this->repo_name."/.git");
	}

	/**
	 * Execute a git command
	 *
	 * @param string    $cmd
	 * @param array     $params
	 * @param string    $repo_name
	 *
	 * @throws GitException
	 * @return GitWrapper 
	 */
	protected function gitExec($cmd, array $params, $repo_name = "")
	{
		assert('is_string($cmd)');
		assert('is_string($repo_name)');

		// remove spaces and avoid shell piping
		$clean = array_map(function ($i) {
				return escapeshellarg(trim($i));
			}, $params);

		$this->process->setWorkingDirectory($this->path . '/' . $repo_name);
		$this->process->setCommandLine($cmd." ".implode(' ', $clean));
		$this->process->setTty(true);
		$this->process->run();

		if(!$this->process->isSuccessful())
		{
			throw new GitException("\nError while executing git command '" . $cmd . "'\n", $result);
		}
		$this->out = $this->process->getOutput();
		return $this;
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
	 * Get repo name
	 *
	 * @return string
	 */
	public function gitGetName()
	{
		return basename($this->repo_url, '.git');
	}

	/**
	 * @inheritdoc
	 */
	public function gitGetBranches()
	{
		try
		{
			$this->gitExec("git branch", array(), $this->repo_name);
		}
		catch(GitException $e)
		{
			echo($e);
		}
		$this->out = array_map(function ($i) {
			return trim(str_replace("*", " ", $i));
		},$this->out);
		return $this->out;
	}
}