<?php
/**
 * TODO: There is a license info missing here. (And most probably in other files
 *       as well)
 * TODO: I would prefer to put interfaces on the top level and put the
 *       Implementations in subfolder. The interfaces are the surface, not the
 *       implementations.
 */
namespace CaT\InstILIAS\interfaces;

/**
 * TODO: This needs to be documented.
 */
interface Git {
	public function cloneGitTo($ilias_git_url, $installation_path);
	public function checkoutBranch($ilias_git_branch_name, $installation_path);
}
