<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Config;

/**
 * Configuration for User Accounts.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 *
 * @method int registration()
 * @method int linkLifetime()
 * @method array requiredFields()
 * @method array users()
 */
class Users extends Base {
	/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			( "registration" => array("int", false)
			, "link_lifetime" => array("int", false)
			, "required_fields" => array(array("string"), true)
			, "users" => array(array("\\CaT\\Ilse\\Config\\User"), true)
			);
	}

	protected static $valid_required_fields = array(
			"title"
			,"birthday"
			,"gender"
			,"institution"
			,"department"
			,"street"
			,"zipcode"
			,"city"
			,"country"
			,"phone_office"
			,"phone_home"
			,"phone_mobile"
			,"fax"
			,"email"
			,"matriculation"
	);

	public function getBasicFields() {
		return self::$valid_required_fields;
	}

	/**
	 * @inheritdocs
	 */
	protected function checkValueContent($key, $value) {
		switch($key) {
			case "required_fields":
				foreach ($value as $field) {
					if (!$this->checkContentValueInArray($field, self::$valid_required_fields)) {
						return false;
					}
				}
				return true;
			default:
				return parent::checkValueContent($key, $value);
		}
	}
}
