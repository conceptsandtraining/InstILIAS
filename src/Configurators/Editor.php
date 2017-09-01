<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS editor part
 * 
 * Enable and configurat Page editor
 * Enable TinyMCE
 */
class Editor {
	/**
	 * @var \ilDB
	 */
	protected $gDB;
	/**
	 * @var \ilSetting
	 */
	protected $gSetting;

	public function __construct($absolute_path, $db, \ilSetting $settings) {
		require_once($absolute_path."/Services/Object/classes/class.ilObjectFactory.php");
		require_once($absolute_path."/Services/COPage/classes/class.ilPageEditorSettings.php");
		require_once($absolute_path."/Services/COPage/classes/class.ilPageContentGUI.php");

		$this->gDB = $db;
		$this->gSetting = $settings;
	}

	/**
	 * Activate the TinyMCE Editor
	 *
	 * @param \CaT\Ilse\Config\Editor $editor
	 */
	public function tinyMCE(\CaT\Ilse\Config\Editor $editor) {
		$obj_id = $this->getObjIdByType("adve");

		$object = \ilObjectFactory::getInstanceByObjId($obj_id);

		if((bool)$editor->enableTinymce()) {
			$object->setRichTextEditor("tinymce");
		} else {
			$object->setRichTextEditor("");
		}

		$object->update();
	}

	/**
	 * Activate the repository page editor
	 *
	 * @param \CaT\Ilse\Config\Editor $editor
	 */
	public function repoPageEditor(\CaT\Ilse\Config\Editor $editor) {
			$repoPageEdit = $editor->repoPageEditor();

			$buttons = \ilPageContentGUI::_getCommonBBButtons();
			foreach ($buttons as $b => $t)
			{
				\ilPageEditorSettings::writeSetting("rep", "active_".$b,
					$repoPageEdit->valueFor($b));
			}

			$this->gSetting->set("enable_cat_page_edit", (int) $repoPageEdit->enable());
	}



	/**
	 * Get the obj_id from type
	 *
	 * @param string $type
	 *
	 * @return int
	 */
	protected function getObjIdByType($type) {
		$query = "SELECT obj_id FROM object_data WHERE type = ".$this->gDB->quote($type, 'text');
		$res = $this->gDB->query($query);
		$row = $this->gDB->fetchAssoc($res);

		return $row["obj_id"];
	}
}