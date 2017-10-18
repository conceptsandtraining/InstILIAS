<?php
namespace CaT\Ilse\Setup;

use CaT\Ilse\Config;
use CaT\Ilse\Aux\TaskLogger;
use CaT\Ilse\Aux\UpdatePluginsHelper;
use CaT\Ilse\Setup\InitILIAS;

/**
 * Class PluginAdministration52
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
class PluginAdministration52 implements PluginAdministration, InitILIAS
{
	const PLUGIN_CLASS_PREFIX_IL 	= "il";
	const PLUGIN_CLASS_PREFIX_CLASS = "class.";
	const PLUGIN_CLASS_SUFFIX 		= "Plugin";
	const PLUGIN_PREFIX 			= "ilias-plugin-";
	const PLUGIN_MAIN_PATH 			= "Customizing/global/plugins";
	const CLASSES_FOLDER 			= "classes";

	/**
	 * @var Config\General
	 */
	protected $config;

	/**
	 * @var TaskLogger
	 */
	protected $logger;

	/**
	 * @var UpdatePluginsHelper
	 */
	protected $update_plugin_helper;

	/**
	 * Constructor of the class PluginAdministration52
	 */
	public function __construct(Config\General $config, TaskLogger $logger, UpdatePluginsHelper $update_plugin_helper)
	{
		$this->config = $config;
		$this->logger = $logger;
		$this->update_plugin_helper = $update_plugin_helper;
		$this->initILIASIsNotInitialized();
	}

	/**
	 * 
	 */
	public function install($name)
	{
		assert('is_string($name)');
		$plugin = $this->getPluginObject($name, false);

		\ilPlugin::createPluginRecord(
			$plugin->getComponentType(),
			$plugin->getComponentName(),
			$plugin->getSlotId(),
			$name);
	}

	/**
	 * @inheritdoc
	 */
	public function update($name)
	{
		assert('is_string($name)');

		$plugin = $this->getPluginObject($name);
		$plugin->update();
	}

	/**
	 * @inheritdoc
	 */
	public function activate($name)
	{
		assert('is_string($name)');

		$plugin = $this->getPluginObject($name);
		$plugin->activate();
	}

	/**
	 * @inheritdoc
	 */
	public function updateLanguage($name)
	{
		assert('is_string($name)');

		$plugin = $this->getPluginObject($name);
		$plugin->updateLanguages();
	}

	/**
	 * @inheritdoc
	 */
	public function uninstall($name)
	{
		assert('is_string($name)');

		// necessary for plugins that not installed via ilse
		try{
			$plugin = $this->getPluginObject($name);
		} catch (\Exception $e) {
			$this->install($name);
			$plugin = $this->getPluginObject($name);
		}

		$plugin->deactivate();
		$plugin->uninstall();
	}

	/**
	 * @inheritdoc
	 */
	public function needsUpdate($name)
	{
		assert('is_string($name)');

		return $this->getPluginObject($name)->needsUpdate();
	}

	/**
	 * @inheritdoc
	 */
	public function getPluginObject($plugin_name, $call_construct = true) {
		assert('is_string($plugin_name)');

		$full_class_name = self::PLUGIN_CLASS_PREFIX_IL.$plugin_name.self::PLUGIN_CLASS_SUFFIX;

		$cur = getcwd();
		chdir($this->config->server()->absolutePath());
		if(!class_exists($full_class_name)) {
			$link = $this->update_plugin_helper->getPluginLinkPath(self::PLUGIN_PREFIX.$plugin_name);
			require_once($link["path"]."/".$link["name"]."/".self::CLASSES_FOLDER."/".self::PLUGIN_CLASS_PREFIX_CLASS.$full_class_name.".php");
		}
		$class = new \ReflectionClass(self::PLUGIN_CLASS_PREFIX_IL.$plugin_name.self::PLUGIN_CLASS_SUFFIX);

		if($call_construct) {
			return $class->newInstance();
		} else {
			return $class->newInstanceWithoutConstructor();
		}
		chdir($cur);
	}

	public function initILIASIsNotInitialized()
	{
		if(isset($GLOBALS['DIC'])){
			return;
		}

		$cur = getcwd();
		chdir($this->config->server()->absolutePath());

		require_once("./Services/Context/classes/class.ilContext.php");
		require_once("./Services/Init/classes/class.ilInitialisation.php");
		define("CLIENT_ID", $this->config->client()->name());

		\ilContext::init(\ilContext::CONTEXT_UNITTEST);
		\ilInitialisation::initILIAS();
		chdir($cur);
	}
}
