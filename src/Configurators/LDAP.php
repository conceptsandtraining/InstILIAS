<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS ldap part
 * 
 * Configure LDAP
 */
class LDAP {
	use ConfigHelper;

	/**
	 * @var \ilDB
	 */
	protected $gDB;

	public function __construct($absolute_path, \ilDBInterface $db) {
		require_once($absolute_path."/Services/LDAP/classes/class.ilLDAPServer.php");
		require_once($absolute_path."/Services/LDAP/classes/class.ilLDAPAttributeMapping.php");
		$this->gDB = $db;
	}
	/**
	 * configures the LDAP server settings for login
	 *
	 * @param \CaT\Ilse\Config\LDAP $ldap_config
	 */
	public function configureLDAPServer(\CaT\Ilse\Config\LDAP $ldap_config) {
		$server = new \ilLDAPServer(0);

		$server->toggleActive(1);
		$server->enableAuthentication(true);
		$server->setName($ldap_config->name());
		$server->setUrl($ldap_config->server());
		$server->setVersion($ldap_config->protocolVersion());
		$server->setBaseDN($ldap_config->basedn());
		$server->setBindingType($ldap_config->conType());
		$server->setBindUser($ldap_config->conUserDn());
		$server->setBindPassword($ldap_config->conUserPw());
		$server->setUserScope($ldap_config->userSearchScope());
		$server->setUserAttribute($ldap_config->attrNameUser());
		$server->enableSyncOnLogin($ldap_config->syncOnLogin());
		$server->enableSyncPerCron($ldap_config->syncPerCron());

		$role_id = $this->getRoleId($ldap_config->registerRoleName());
		$server->setGlobalRole($role_id);

		//group scope is a not null value in database
		//we do not need, but it is necessary to be set
		//1 is the default value
		$server->setGroupScope(1);

		if(!$server->validate()) {
			global $ilErr;
			throw new \Exception("Error creating LDAP Server: ".$ilErr->getMessage());
		}

		$server->create();

		$mapping = \ilLDAPAttributeMapping::_getInstanceByServerId($server->getServerId());
		$mapping->setRule('global_role', $role_id, false);
		$mapping->save();
	}

	public function mapLDAPValues(\CaT\Ilse\Config\LDAP $ldap_config) {
		$server_id = \ilLDAPServer::_getFirstServer();
		$mapping = \ilLDAPAttributeMapping::_getInstanceByServerId($server_id);

		foreach ($ldap_config->mappings()->getAvailableMappings() as $rule) {
			if($ldap_config->mappings()->$rule) {
				$mapping->setRule($rule, $ldap_config->mappings()->$rule, true);
			}
		}

		$mapping->save();
	}
}