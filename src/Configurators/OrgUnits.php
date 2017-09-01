<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS org unit part
 * 
 * Create roles
 */
class OrgUnits {
	/**
	 * @var \ilDB
	 */
	protected $gDB;

	public function __construct($absolute_path, \ilDBInterface $db) {
		require_once($absolute_path."/Modules/OrgUnit/classes/class.ilObjOrgUnit.php");
		require_once($absolute_path."/Modules/OrgUnit/classes/Types/class.ilOrgUnitType.php");
		$this->gDB = $db;
	}

	/**
	 * creates organisational units according to defined structur
	 * recursive
	 *
	 * @param \CaT\Ilse\Config\OrgUnits $install_orgunits
	 */
	public function createOrgUnits(\CaT\Ilse\Config\OrgUnits $install_orgunits) {
		
		foreach ($install_orgunits->orgunits() as $key => $value) {
			$this->createOrgunit($value, \ilObjOrgUnit::getRootOrgRefId());
		}
	}

	/**
	 * single OrgUnit and her children created
	 * recursiv
	 *
	 * @param $org_unit
	 * @param int $parent_ref_id
	 */
	protected function createOrgUnit($org_unit, $parent_ref_id) {
		$orgu = new \ilObjOrgUnit();
		$orgu->setTitle($org_unit->title());
		$orgu->create();
		$orgu->createReference();
		$orgu->update();

		$orgu->putInTree($parent_ref_id);
		$orgu->initDefaultRoles();

		foreach ($org_unit->children() as $key => $value) {
			$this->createOrgUnit($value, $orgu->getRefId());
		}
	}

	/**
	 *
	 *
	 * @param \CaT\Ilse\Config\OrgunitTypes $orgunit_types
	 */
	public function createOrgunitTypes(\CaT\Ilse\Config\OrgunitTypes $orgunit_types) {
		foreach ($orgunit_types->orgunitTypes() as $orgunit_type) {
			$this->createOrgunitType($orgunit_type);
		}
	}

	/**
	 *
	 * @param \CaT\Ilse\Config\OrgunitType $orgunit_type
	 */
	protected function createOrgunitType(\CaT\Ilse\Config\OrgunitType $orgunit_type) {
		$type = new \ilOrgUnitType();

		$type->setDefaultLang($orgunit_type->defaultLanguage());
		foreach ($orgunit_type->typeLanguageSettings() as $type_language_setting) {
			$title = $type_language_setting->title();
			$description = $type_language_setting->description();
			$lang_code = $type_language_setting->language();
			$type->setTitle($title, $lang_code);
			$type->setDescription($description, $lang_code);
		}

		$type->save();
	}

	/**
	 *
	 *
	 * @param \CaT\Ilse\Config\OrgunitTypeAssignment $orgunit_type_assignments
	 */
	public function assignOrgunitTypesToOrgunits(\CaT\Ilse\Config\OrgunitTypeAssignments $orgunit_type_assignments) {
		foreach ($orgunit_type_assignments->orgunitTypeAssignments() as $orgunit_type_assignment) {
			$this->orgunitTypeAssignment($orgunit_type_assignment);
		}
	}

	/**
	 *
	 *
	 * @param \CaT\Ilse\Config\OrgunitTypeAssignment $orgunit_type_assignment
	 */
	protected function orgunitTypeAssignment(\CaT\Ilse\Config\OrgunitTypeAssignment $orgunit_type_assignment) {
		$orgunit_id = $this->getOrgunitId($orgunit_type_assignment->orgunitTitle());
		$orgunit_type_id = $this->getOrgunitTypeId($orgunit_type_assignment->orgunitTypeTitle(),$orgunit_type_assignment->orgunitTypeDefaultLanguage());

		if(!$orgunit_id || !$orgunit_type_id) {
			echo "No orgunit or orgunit type found";
			return;
		}

		$orgunit = new \ilObjOrgUnit($orgunit_id, false);
		$orgunit->setOrgUnitTypeId($orgunit_type_id);
		$orgunit->update();
	}

	/**
	 *
	 *
	 * @param string $orgunit_tite
	 *
	 * @return integer
	 */
	protected function getOrgunitId($orgunit_tite) {
		$select = "SELECT obj_id\n"
				 ." FROM object_data\n"
				 ." WHERE title = ".$this->gDB->quote($orgunit_tite,"text")
				 ."    AND type = 'orgu'";
		$res = $this->gDB->query($select);
		if($this->gDB->numRows($res) == 1) {
			$row = $this->gDB->fetchAssoc($res);
			return $row["obj_id"];
		}

		return null;
	}

	/**
	 *
	 *
	 * @param string $orgunit_type_title
	 * @param string $orgunit_default_lang
	 *
	 * @return integer
	 */
	protected function getOrgunitTypeId($orgunit_type_title, $orgunit_default_lang) {
		$select = "SELECT orgu_types.id\n"
				 ." FROM orgu_types\n"
				 ." JOIN orgu_types_trans ON orgu_types.id = orgu_types_trans.orgu_type_id"
				 ." WHERE member = ".$this->gDB->quote("title","text")
				 ."    AND value = ".$this->gDB->quote($orgunit_type_title,"text")
				 ."    AND lang = ".$this->gDB->quote($orgunit_default_lang,"text");

		$res = $this->gDB->query($select);
		if($this->gDB->numRows($res) == 1) {
			$row = $this->gDB->fetchAssoc($res);
			return $row["id"];
		}

		return null;
	}
}