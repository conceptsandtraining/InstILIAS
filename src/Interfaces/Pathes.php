<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Interfaces;

interface Pathes
{
	/**
	 * Get the current working directory
	 *
	 * @return string
	 */
	public function getCWD();

	/**
	 * Get the home directory of the current user
	 *
	 * @return string
	 */
	public function getHomeDir();

	/**
	 * Convert ~ to actual home path
	 *
	 * @param string 		$path
	 * @return string
	 */
	public function convertTilde($path);
}