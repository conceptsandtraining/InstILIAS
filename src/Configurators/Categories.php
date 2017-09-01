<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS categories part
 * 
 * Create roles
 */
class Categories {
	/**
	 * @var \ilTree
	 */
	protected $gTree;

	public function __construct($absolute_path, \ilTree $tree) {
		require_once($absolute_path."/Modules/Category/classes/class.ilObjCategory.php");
		$this->gTree = $tree;
	}

	/**
	 * creates categories units according to defined structur
	 * recursive
	 *
	 * @param \CaT\Ilse\Config\OrgUnits $install_categories
	 */
	public function createCategories(\CaT\Ilse\Config\Categories $install_categories) {
		foreach ($install_categories->categories() as $key => $value) {
			$this->createCategory($value, $this->gTree->getRootId());
		}
	}

	/**
	 * single Category and her children created
	 * recursiv
	 *
	 * @param $category
	 * @param int $parent_ref_id
	 */
	protected function createCategory($category, $parent_ref_id) {
		$cat = new \ilObjCategory();
		$cat->setTitle($category->title());
		$cat->create();
		$cat->createReference();
		$cat->update();

		$cat->putInTree($parent_ref_id);
		$cat->initDefaultRoles();

		foreach ($category->children() as $key => $value) {
			$this->createCategory($value, $cat->getRefId());
		}
	}
}