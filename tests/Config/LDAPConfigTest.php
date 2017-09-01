<?php

require_once(__DIR__."/ConfigTestHelper.php");

use \CaT\Ilse\Config\LDAP;
use \CaT\Ilse\Config\LDAPMappings;

class LDAPConfigTest extends PHPUnit_Framework_TestCase{
	use ConfigTestHelper;

	public function test_not_enough_params() {
		try {
			$config = new LDAP();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	/**
	 * @dataProvider	LDAPConfigValueProvider
	 */
	public function test_LDAPConfig($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login
									, $sync_per_cron, $userGroup, $attrNameUser, $protocolVersion, $userSearchScope, $registerRoleName, $mappings, $valid) 
	{
		if ($valid) {
			$this->_test_valid_LDAPConfig($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login
										, $sync_per_cron, $userGroup, $attrNameUser, $protocolVersion, $userSearchScope, $registerRoleName, $mappings);
		}
		else {
			$this->_test_invalid_LDAPConfig($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login
										, $sync_per_cron, $userGroup, $attrNameUser, $protocolVersion, $userSearchScope, $registerRoleName, $mappings);
		}
	}

	public function _test_valid_LDAPConfig($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login
											, $sync_per_cron, $userGroup, $attrNameUser, $protocolVersion, $userSearchScope, $registerRoleName, $mappings) 
	{
		$config = new LDAP($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login, $sync_per_cron, $userGroup, $attrNameUser
							, $protocolVersion, $userSearchScope, $registerRoleName, $mappings);
		$this->assertEquals($name, $config->name());
		$this->assertEquals($basedn, $config->basedn());
		$this->assertEquals($conType, $config->conType());
		$this->assertEquals($conUserDn, $config->conUserDn());
		$this->assertEquals($conUserPw, $config->conUserPw());
		$this->assertEquals($sync_on_login, $config->syncOnLogin());
		$this->assertEquals($sync_per_cron, $config->syncPerCron());
		$this->assertEquals($userGroup, $config->userGroup());
		$this->assertEquals($attrNameUser, $config->attrNameUser());
		$this->assertEquals($protocolVersion, $config->protocolVersion());
		$this->assertEquals($userSearchScope, $config->userSearchScope());
		$this->assertEquals($registerRoleName, $config->registerRoleName());
		$this->assertEquals($mappings, $config->mappings());
	}

	public function _test_invalid_LDAPConfig($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login
											, $sync_per_cron, $userGroup, $attrNameUser, $protocolVersion, $userSearchScope, $registerRoleName, $mappings)
	{
		try {
			$config = new LDAP($name, $server, $basedn, $conType, $conUserDn, $conUserPw, $sync_on_login, $sync_per_cron, $userGroup, $attrNameUser
								, $protocolVersion, $userSearchScope, $registerRoleName, $mappings);
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function LDAPConfigValueProvider() {
		return $this->buildProviderCombinations(
			[ "nameProvider"
			, "serverProvider"
			, "basednProvider"
			, "conTypeProvider"
			, "conUserDnProvider"
			, "conUserPwProvider"
			, "syncOnLoginProvider"
			, "syncPerCronProvider"
			, "userGroupProvider"
			, "attrNameUserProvider"
			, "protocolVersionProvider"
			, "userSearchScopeProvider"
			, "registerRoleNameProvider"
			, "mappingsProvider"
			]); 
	}

	public function nameProvider() {
		return array(
				array("ldap", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function serverProvider() {
		return array(
				array("ldap://127.0.0.1:389", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function basednProvider() {
		return array(
				array("cn=user,dc=dvdom,dc=local", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function conTypeProvider() {
		return array(
				array(1, true)
				, array("2", false)
				, array(true, false)
				, array(0, true)
				, array(array(), false)
			);
	}

	public function conUserDnProvider() {
		return array(
				array("cn=ldap,cn=user,dc=dcdom,dc=local", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function conUserPwProvider() {
		return array(
				array("abcd", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function syncOnLoginProvider() {
		return array(
				array(0, true)
				, array("2", false)
				, array(true, false)
				, array(1, true)
				, array(array(), false)
			);
	}

	public function syncPerCronProvider() {
		return array(
				array(0, true)
				, array("2", false)
				, array(true, false)
				, array(1, true)
				, array(array(), false)
			);
	}

	public function userGroupProvider() {
		return array(
				array("benutzer", true)
				, array(true, false)
				, array(1, false)
				, array(array(), false)
			);
	}

	public function attrNameUserProvider() {
		return array(
				array("sAMAccountName", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function protocolVersionProvider() {
		return array(
				array(2, true)
				, array("2", false)
				, array(true, false)
				, array(3, true)
				, array(array(), false)
			);
	}

	public function userSearchScopeProvider() {
		return array(
				array(1, true)
				, array("2", false)
				, array(true, false)
				, array(0, true)
				, array(array(), false)
			);
	}

	public function registerRoleNameProvider() {
		return array(
				array("User", true)
				, array(2, false)
				, array(true, false)
				, array(array(), false)
			);
	}

	public function mappingsProvider() {
		return array(
				array( new LDAPMappings
					( "firstname"
					, "lastname"
					, "department"
					, "email"
					, "fax"
					, "gender"
					, "hobby"
					, "institution"
					, "matriculation"
					, "phone_home"
					, "phone_mobile"
					, "phone_office"
					, "street"
					, "title"
					, "zipcode"
					, "city"
					, "country"
					), true)
			);
	}
}
