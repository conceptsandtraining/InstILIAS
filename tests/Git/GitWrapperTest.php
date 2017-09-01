<?php
require_once(__DIR__."/GitTest.php");

/**
 * Test class for git commands
 * 
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 */
class GitWrapperTest extends GitTest
{
	public function getImplementation($target_dir, $remote_url, $repo_name)
	{
		// TODO: this should not rely on connectivity to github. it could use this repo instead.
		return new \CaT\Ilse\Git\GitWrapper($target_dir, $remote_url, $repo_name);
	}
}
