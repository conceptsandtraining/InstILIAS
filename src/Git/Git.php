<?php

/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Git;

/**
 * Wrapper for git commands
 *
 * TODO: remove that `git`-prefix: gitClone -> clone...
 * 
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @author Richard Klees <richard.klees@concepts-and-training.de>
 */
interface Git
{
		/**
		 * Clone a repository by a given URL
		 *
		 * @throws Exception
		 * @return void
		 */
		public function gitClone();

		/**
		 * Checkout a branch by name
		 *
		 * @param string    $path
		 * @param boolean   $new
		 *
		 * @throws Exception
		 * @return void
		 */
		public function gitCheckOut($branch, $new = false);

		/**
		 * Pull a repo
		 *
		 * @param string    $remote
		 * @param string    $branch
		 *
		 * @throws Exception
		 * @return void
		 */
		public function gitPull($branch);

		/**
		 * Fetch a repo
		 *
		 * @param string    $remote
		 *
		 * @throws Exception
		 * @return void
		 */
		public function gitFetch();
}
