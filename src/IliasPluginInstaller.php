<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\InstILIAS;
/**
 * implementation of plugin interface to install plugins
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 */
class IliasPluginInstaller implements \CaT\InstILIAS\interfaces\Plugin {
	const PLUGIN_TMP_FOLDER = "plugin_tmp";
	const PLUGIN_CLASS_PREFIX_IL = "il";
	const PLUGIN_CLASS_PREFIX_CLASS = "class.";
	const PLUGIN_CLASS_SUFFIX = "Plugin";
	const CLASSES_FOLDER = "classes";
	const PLUGIN_MAIN_PATH = "Customizing/global/plugins";

	private static $slot_names = array("ilOrgUnitExtensionPlugin" => "OrgUnitExtension"
										,"ilOrgUnitTypeHookPlugin" => "OrgUnitTypeHook"
										,"ilSurveyQuestionsPlugin" => "SurveyQuestions"
										,"ilExportPlugin" => "Export"
										,"ilSignaturePlugin" => "Signature"
										,"ilQuestionsPlugin" => "Questions"
										,"ilAdvancedMDClaimingPlugin" => "AdvancedMDClaiming"
										,"ilAuthenticationHookPlugin" => "AuthenticationHook"
										,"ilShibbolethAuthenticationHookPlugin" => "ShibbolethAuthenticationHook"
										,"ilPageComponentPlugin" => "PageComponent"
										,"ilCronHookPlugin" => "CronHook"
										,"ilEventHookPlugin" => "EventHook"
										,"ilLDAPHookPlugin" => "LDAPHook"
										,"ilPersonalDesktopHookPlugin" => "PersonalDesktopHook"
										,"ilPreviewRendererPlugin" => "PreviewRenderer"
										,"ilRepositoryObjectPlugin" => "RepositoryObject"
										,"ilUserInterfaceHookPlugin" => "UserInterfaceHook"
										,"ilUDFClaimingPlugin" => "UDFClaiming"
								);

	protected $gDB;
	protected $absolute_path;
	protected $temp_folder;
	protected $installed_plugins;

	public function __construct($absolute_path, $gDB) {
		$this->gDB = $gDB;
		$this->absolute_path = $absolute_path;
		$this->temp_folder = $absolute_path."/".self::PLUGIN_TMP_FOLDER;
		$this->installed_plugins = array();

		$this->createTempFolder();
		$this->readInstalledPlugins();
	}

	public function __destruct() {
		$this->deleteTempFolder($this->temp_folder);
	}

	/**
	 * @inheritdoc
	 */
	public function install(\CaT\InstILIAS\Config\Plugin $plugin) {
		$this->checkout($plugin->git()->url(), $plugin->git()->branch(), $this->temp_folder."/".$plugin->name());

		$plugin_path = $this->getPluginPath($this->temp_folder, $plugin->name());

		$this->movePlugin($this->temp_folder."/".$plugin->name(), $this->absolute_path."/".$plugin_path);

		$this->createPluginRecord($plugin->name());

		$this->installed_plugins[$plugin->name()] = $this->absolute_path."/".$plugin_path;

		return true;
	}

	public function updateBranch(\CaT\InstILIAS\Config\Plugin $plugin) {
		$pl = $this->getPluginObject($plugin->name());
		$plugin_path = $pl->getDirectory();
		$this->checkout($plugin->git()->url(), $plugin->git()->branch(), $plugin_path);
	}

	/**
	 * @inheritdoc
	 */
	public function isInstalled(\CaT\InstILIAS\Config\Plugin $plugin) {
		if(empty($this->installed_plugins)) {
			return false;
		}
		return array_key_exists($plugin->name(), $this->installed_plugins);
	}

	/**
	 * @inheritdoc
	 */
	public function update(\CaT\InstILIAS\Config\Plugin $plugin) {
		$pl = $this->getPluginObject($plugin->name());

		if($this->needsUpdate($pl)) {
			$pl->update();
		}

		return !$this->needsUpdate($pl);
	}

	/**
	 * @inheritdoc
	 */
	public function activate(\CaT\InstILIAS\Config\Plugin $plugin) {
		$pl = $this->getPluginObject($plugin->name());

		if($this->needsUpdate($pl)) {
			$pl->update();
		}

		if(!$pl->isActive()) {
			$pl->activate();
		}

		return $pl->isActive();
	}

	/**
	 * @inheritdoc
	 */
	public function deactivate(\CaT\InstILIAS\Config\Plugin $plugin) {
		$pl = $this->getPluginObject($plugin->name());

		if($pl->isActive()) {
			$pl->deactivate();
		}

		return !$pl->isActive();
	}

	/**
	 * @inheritdoc
	 */
	public function updateLanguage(\CaT\InstILIAS\Config\Plugin $plugin) {
		$pl = $this->getPluginObject($plugin->name());
		$pl->updateLanguages();
	}

	/**
	 * @inheritdoc
	 */
	public function getPluginObject($plugin_name, $call_construct = true) {
		assert('is_string($plugin_name)');
		$full_class_name = self::PLUGIN_CLASS_PREFIX_IL.$plugin_name.self::PLUGIN_CLASS_SUFFIX;

		if(!class_exists($full_class_name)) {
			require_once($this->getInstalledPluginPath($plugin_name)."/".$plugin_name."/".self::CLASSES_FOLDER."/".self::PLUGIN_CLASS_PREFIX_CLASS.$full_class_name.".php");
		}
		$class = new \ReflectionClass(self::PLUGIN_CLASS_PREFIX_IL.$plugin_name.self::PLUGIN_CLASS_SUFFIX);

		if($call_construct) {
			return $class->newInstance();
		} else {
			return $class->newInstanceWithoutConstructor();
		}
		
	}

	/**
	 * @inheritdoc
	 */
	public function uninstall($plugin_name) {
		$plugin = $this->getPluginObject($plugin_name);
		$plugin->uninstall();
	}

	/**
	 * clone plugin into plugin temp folder
	 *
	 * @param string $git_url
	 * @param string $git_branch
	 * @param string $temp_folder
	 */
	protected function checkout($git_url, $git_branch, $temp_folder) {
		assert('is_string($git_url)');
		assert('is_string($git_branch)');
		assert('is_string($temp_folder)');

		$git = new \CaT\InstILIAS\GitExecuter;

		try {
			$git->cloneGitTo($git_url, $git_branch, $temp_folder);
		} catch(\RuntimeException $e) {
			echo $e->getMessage();
			die(1);
		}
	}

	/**
	 * move pluin to the determined destination folder
	 *
	 * @param string $temp_folder
	 * @param string $destination_folder
	 */
	protected function movePlugin($temp_folder, $destination_folder) {
		assert('is_string($temp_folder)');
		assert('is_string($destination_folder)');

		if(!is_dir($destination_folder)) {
			$this->createFolder($destination_folder);
		}

		rename($temp_folder, $destination_folder);
	}

	/**
	 * creates the plugin temp folder if not exist
	 */
	protected function createTempFolder() {
		if(!is_dir($this->temp_folder)) {
			$this->createFolder($this->temp_folder);
		}
	}

	protected function createFolder($dir) {
		mkdir($dir, 0755, true);
	}

	/**
	 * deletes the plugin temp folder
	 * recursive
	 *
	 * @param string $temp_folder
	 */
	public function deleteTempFolder($temp_folder) {
		assert('is_string($temp_folder)');

		$files = array_diff(scandir($temp_folder), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$temp_folder/$file")) ? $this->deleteTempFolder("$temp_folder/$file") : unlink("$temp_folder/$file");
		}

		rmdir($temp_folder);
	}

	/**
	 * get the name of plugin
	 * 
	 * @param string $temp_folder
	 *
	 * @return string
	 */
	protected function determineName($temp_folder) {
		assert('is_string($temp_folder)');
		$files = array_diff(scandir($temp_folder), array('.','..'));
		assert('count($files) == 1');

		if(!is_dir($temp_folder."/".$files[0])) {
			echo "There is no plugin folder in plugin temp folder.";
			die(1);
		}

		return $files[0];
	}

	/**
	 * @inheritdoc
	 */
	public function needsUpdate(\ilPlugin $plugin) {
		$last_update 	= $plugin->getLastUpdateVersion();
		$curr_version 	= $plugin->getVersion();

		return $last_update < $curr_version;
	}

	protected function getPluginPath($temp_folder, $plugin_name) {

		assert('is_string($temp_folder)');
		assert('is_string($plugin_name)');

		$full_class_name = self::PLUGIN_CLASS_PREFIX_IL.$plugin_name.self::PLUGIN_CLASS_SUFFIX;

		require_once($temp_folder."/".$plugin_name."/".self::CLASSES_FOLDER."/".self::PLUGIN_CLASS_PREFIX_CLASS.$full_class_name.".php");
		$class = new \ReflectionClass($full_class_name);
		$plugin = $class->newInstanceWithoutConstructor();
		$path = self::PLUGIN_MAIN_PATH."/".$plugin->getComponentType()."/".$plugin->getComponentName()."/".$plugin->getSlot()."/".$plugin_name;

		return $path;
	}

	protected function getPluginSlotData($parent_name) {
		$query = "SELECT component, id, name FROM il_pluginslot WHERE name = ".$this->gDB->quote(self::$slot_names[$parent_name], "text");
		echo $query;
		$res = $this->gDB->query($query);
		return $this->gDB->fetchAssoc($res);
	}

	protected function getInstalledPluginPath($plugin_name) {
		return $this->installed_plugins[$plugin_name];
	}

	protected function readInstalledPlugins() {
		$plugin_main_path = $this->absolute_path."/".self::PLUGIN_MAIN_PATH;
		$this->dir($plugin_main_path);
	}

	protected function dir($path) {
		if(is_dir($path)) {
			$files = array_diff(scandir($path), array('.','..'));
			foreach ($files as $key => $file) {
				if(is_dir($path."/".$file) && !in_array($file, self::$slot_names)) {
					$this->dir($path."/".$file);
				} else if(is_dir($path."/".$file) && in_array($file, self::$slot_names)) {
					$this->readPlugins($path."/".$file);
				}
			}
		}

		return;
	}

	protected function readPlugins($path) {
		$plugins = array_diff(scandir($path), array('.','..'));
		foreach ($plugins as $key => $plugin) {
			$this->installed_plugins[$plugin] = $path;
		}
	}

	protected function createPluginRecord($plugin_name) {
		$pl = $this->getPluginObject($plugin_name, false);

		require_once($this->absolute_path."/Services/Component/classes/class.ilPlugin.php");
		\ilPlugin::createPluginRecord($pl->getComponentType(), $pl->getComponentName(), $pl->getSlotId(), $plugin_name);
	}
}