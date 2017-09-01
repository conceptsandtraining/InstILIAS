<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS user part
 * 
 * Create user accounts
 * Enable registration
 * Change user basic field requiremtns
 * Password settings
 */
class Users {
	use ConfigHelper;

	/**
	 * @var \ilRegistrationSettings
	 */
	protected $registration_settings;
	/**
	 * @var \ILIAS
	 */
	protected $gIlias;
	/**
	 * @var \ilObjUser
	 */
	protected $gUser;
	/**
	 * @var \ilRbacadmin
	 */
	protected $gRbacadmin;
	/**
	 * @var \ilDB
	 */
	protected $gDB;

	public function __construct($absolute_path, \ILIAS $ilias, \ilObjUser $user, \ilRbacadmin $rbacadmin, $db) {
		require_once($absolute_path."/Services/User/classes/class.ilObjUser.php");
		require_once($absolute_path."/Services/PrivacySecurity/classes/class.ilSecuritySettings.php");
		require_once($absolute_path."/Services/Utilities/classes/class.ilUtil.php");

		$this->gIlias = $ilias;
		$this->gUser = $user;
		$this->gRbacadmin = $rbacadmin;
		$this->gDB = $db;
	}

	/**
	 * Configure the type of self registration
	 *
	 * @param \CaT\Ilse\Config\Users $users
	 */
	public function registration(\CaT\Ilse\Config\Users $users) {
		require_once './Services/Registration/classes/class.ilRegistrationSettings.php';
		$this->registration_settings = new \ilRegistrationSettings();

		$this->registration_settings->setRegistrationType((int)$users->registration());
		$this->registration_settings->setRegistrationHashLifetime(max((int)$users->linkLifetime(), \ilRegistrationSettings::REG_HASH_LIFETIME_MIN_VALUE));

		$this->registration_settings->save();
	}

	/**
	 *
	 *
	 * @param \CaT\Ilse\Config\Users $users
	 */
	public function createUserAccounts(\CaT\Ilse\Config\Users $users) {
		foreach ($users->users() as $user) {
			echo "\nCreating user account for :".$user->email()."...";
			$password = $this->createUser($user);
			echo "\tDone. Initialize password: ".$password;
		}
	}

	protected function createUser(\CaT\Ilse\Config\User $user) {

		if(!\ilObjUser::_lookupId($user->login())) {
			$new_user = new \ilObjUser();

			$new_user->setTimeLimitUnlimited(true);
			$new_user->setTimeLimitOwner($this->gUser->getId());
			$new_user->setLogin($user->login());
			$new_user->setGender($user->gender());

			$new_user->setFirstname($user->firstname());
			$new_user->setLastname($user->lastname());
			$new_user->setEmail($user->email());
			$new_user->setActive(true);

			$password = $this->generatePasswort();
			$new_user->setPasswd($password, IL_PASSWD_PLAIN);
			$new_user->setTitle($new_user->getFullname());
			$new_user->setDescription($new_user->getEmail());

			$new_user->create();

			$new_user->setLastPasswordChangeTS(time());
			$new_user->saveAsNew();

			$this->gRbacadmin->assignUser($this->getRoleId($user->role()), $new_user->getId(),true);

			$new_user->setProfileIncomplete(true);
			$new_user->update();

			return $password;
		}

		return "User with login ".$user->login()." has been created yet. No new User was created.";
	}

	/**
	 * Change the settings of required basic user fields.
	 *
	 * @param \CaT\Ilse\Config\User $user
	 */
	public function changeRequirementSettings(\CaT\Ilse\Config\Users $user) {
		$required_fields = $user->requiredFields();

		foreach ($user->getBasicFields() as $field) {
			if(is_array($required_fields) && !empty($required_fields) && in_array($field, $required_fields)) {
				$this->gIlias->setSetting("require_".$field, "1");
			} else {
				$this->gIlias->deleteSetting("require_".$field);
			}
		}
	}

	protected function generatePasswort() {
		$pwd = \ilUtil::generatePasswords(1);
		return $pwd[0];
	}

	/**
	 * Configure the passwort settings
	 *
	 * @param \CaT\Ilse\Config\PasswordSettings $password_settings
	 */
	public function passwordSettings(\CaT\Ilse\Config\PasswordSettings $password_settings) {
			$security = \ilSecuritySettings::_getInstance();

			// account security settings
			$security->setPasswordCharsAndNumbersEnabled((bool) $password_settings->numbersAndChars());
			$security->setPasswordSpecialCharsEnabled((bool) $password_settings->useSpecialChars());
			$security->setPasswordMinLength((int) $password_settings->minLength());
			$security->setPasswordMaxLength((int) $password_settings->maxLength());
			$security->setPasswordNumberOfUppercaseChars((int) $password_settings->numUpperChars());
			$security->setPasswordNumberOfLowercaseChars((int) $password_settings->numLowerChars());
			$security->setPasswordMaxAge((int) $password_settings->expireInDays());
			$security->setLoginMaxAttempts((int) $password_settings->maxNumLoginAttempts());
			$security->setPasswordChangeOnFirstLoginEnabled((bool) $password_settings->forgotPasswordAktive());

			$security->save();
	}
}