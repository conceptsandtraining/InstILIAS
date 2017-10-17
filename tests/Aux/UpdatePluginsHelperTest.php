<?php

/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

use \CaT\Ilse\Action\UpdatePluginsDirectory;
use \CaT\Ilse\Aux\UpdatePluginsHelper;
use \CaT\Ilse\Action\UpdatePlugins;
use \CaT\Ilse\Config;
use \CaT\Ilse\IliasReleaseConfigurator;
use \CaT\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\Git;
use CaT\Ilse\Aux;

use CaT\Ilse\Action\UpdatePlugin;

// If database had it own interface like filesystem, we could
// drop this and write a proper test instead.
class UpdatePluginsHelperForTest extends UpdatePluginsHelper{
}

class UpdatePluginsHelperTest extends PHPUnit_Framework_TestCase
{
	public function test_perform()
	{
	}
}