<?php
namespace CaT\Ilse\Aux;

use CaT\Ilse\Config\Server;
use CaT\Ilse\Config\Plugins;
use CaT\Ilse\Aux\Filesystem;
use CaT\Ilse\Aux\Yaml;

/**
 * Class UpdatePluginsHelper
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
class UpdatePluginsHelper
{
	const PLUGIN_META = "meta.yaml";
	const BASE_PATH = "Customizing/global/plugins";

	/**
	 * @var array
	 */
	protected $server;

	/**
	 * @var array
	 */
	protected $plugins;

	/**
	 * @var Aux\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var string
	 */
	protected $dir;

	/**
	 * @var Yaml
	 */
	protected $parser;

	/**
	 * Constructor of the class UpdatePluginsHelper
	 */
	public function __construct(
		Server $server,
		Plugins $plugins,
		Filesystem $filesystem,
		Yaml $parser
	) {
		$this->server = $server;
		$this->plugins = $plugins;
		$this->filesystem = $filesystem;
		$this->parser = $parser;
		$this->dir = $this->plugins->dir();
	}

	/**
	 * Get repo urls from config file
	 *
	 * @return string[]
	 */
	public function getRepoUrls()
	{
		$ret = array_map(function($plugin)
			{
				return $plugin->git()->url();
			}
			, $this->plugins->plugins());
		return $ret;
	}

	/**
	 * Get the repo name
	 *
	 * @param 	string 	$url
	 * @return 	string
	 */
	public function getRepoNameFromUrl($url)
	{
		assert('is_string($url)');

		$lastslash = strrpos($url, '/');
		return substr($url, $lastslash+1);
	}

	/**
	 * Get an array of installed plugins with their repo-names.
	 *
	 * @return string[] | []
	 */
	public function getInstalledPlugins()
	{
		if($this->filesystem->exists($this->dir) && !$this->filesystem->isEmpty($this->dir))
		{
			return $this->filesystem->getSubdirectories($this->dir);
		}
		return array();
	}

	/**
	 * Get an array with the ilias path for a plugin and its name
	 *
	 * @param string 	$name
	 * @return string[]
	 */
	public function getPluginLinkPath($name)
	{
		assert('is_string($name)');

		$content = $this->filesystem->read($this->dir."/".$name."/".self::PLUGIN_META);
		$meta = $this->parser->parse($content);
		$absolute_path = $this->server->absolute_path();

		$plugin = array();
		$plugin['path'] =
			$absolute_path."/".
			self::BASE_PATH."/".
			$meta['ComponentType']."/".
			$meta['ComponentName']."/".
			$meta['Slot']
			;
		$plugin['name'] = $meta['Name'];

		return $plugin;
	}

	/**
	 * Get unlisted plugins
	 *
	 * @param 	array 	$installed
	 * @param 	array 	$listed
	 * @return 	string[]
	 */
	public function getUnlistedPlugins(array $installed, array $listed)
	{
		$listed = array_map(function($url) { return $this->getRepoNameFromUrl($url); }, $listed);
		return array_diff($installed, $listed);
	}

	/**
	 * Get dir
	 */
	public function dir()
	{
		return $this->dir;
	}
}