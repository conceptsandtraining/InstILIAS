<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse;
use CaT\Ilse\Git\GitWrapper;
/**
 * Implementation of the git interface.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 */

class GitExecutor implements \CaT\Ilse\Interfaces\Git
{
	const URL_REG_EX = "/^(https:\/\/github\.com)/";

	/**
	 * @inhertidoc
	 */
	public function cloneGitTo($git_url, $git_branch, $installation_path, $name)
	{
		assert('is_string($git_url)');
		assert('is_string($git_branch)');
		assert('is_string($installation_path)');

		$git = new GitWrapper($installation_path, $git_url, $name);

		$cur_dir = getcwd();
		if(is_dir($installation_path."/".$name)) {
			chdir($installation_path."/".$name);
			if(!$git->gitIsGitRepo()) {
				return $git->gitClone();
			}
			$git->gitIgnoreFileModeChanges();
			$git->gitFetch();
			$git->gitCheckout($git_branch, false);
			$git->gitPull("origin", $git_branch);
		} else {
			$git->gitClone();
		}
		chdir($cur_dir);

		return true;
	}
}
