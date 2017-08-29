<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse;

use CaT\Ilse\Interfaces;
/**
 * Serves common pathes
 */
class Pathes implements Interfaces\Pathes
{
	/**
	 * @inheritdoc
	 */
	public function getCWD()
	{
		return getcwd();
	}

	/**
	 * @inheritdoc
	 */
	public function getHomeDir()
	{
		return getenv("HOME");
	}

	/**
	 * @inheritdoc
	 */
	public function expandHomeFolder($path)
	{
		if(strpos($path, '~') !== 0)
		{
			return $path;
		}
		return str_replace("~", $this->getHomeDir(), $path);
	}
}