<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS role part
 * 
 * Create roles
 */
class Roles {
	/**
	 * @var \ilRbacadmin
	 */
	protected $gRbacadmin;

	public function __construct($absolute_path, \ilRbacadmin $rbacadmin) {
		require_once($absolute_path."/Services/AccessControl/classes/class.ilObjRole.php");
		$this->gRbacadmin = $rbacadmin;
	}
	/**
	 * creates global roles
	 *
	 * @param \CaT\Ilse\Config\Roles $install_roles
	 */
	public function createRoles(\CaT\Ilse\Config\Roles $install_roles) {
		foreach ($install_roles->roles() as $role => $value) {
			$newObj = new \ilObjRole();
			$newObj->setTitle($value->title());
			$newObj->setDescription($value->description());
			$newObj->create();

			$this->gRbacadmin->assignRoleToFolder($newObj->getId(), ROLE_FOLDER_ID,'y');
			$this->gRbacadmin->setProtected(ROLE_FOLDER_ID, $newObj->getId(),'n');
		}
	}
}