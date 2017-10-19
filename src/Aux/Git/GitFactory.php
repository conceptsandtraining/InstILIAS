<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Aux\Git;

/**
 * Builds git-instances.
 */
class GitFactory
{
	/**
	 * @param string    $path
	 * @param string    $repo_url
	 * @param bool		$verbose
	 * @return Git
	 */
	public function getRepo($path, $repo_url, $verbose = false)
	{
		return new GitWrapper($path, $repo_url, $verbose);
	}
}
