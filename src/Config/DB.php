<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\InstILIAS\Config;

/**
 * Configuration for an ILIAS database.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 *
 * @method string host()
 * @method string database()
 * @method string user()
 * @method string password()
 * @method string engine()
 * @method string encoding()
 * @method int createDb()
 */
class DB extends Base {

	const IP_REGEX = "/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
	const HOST_NAME_REGEX = "/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])(:\d+)?$/";

	/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			( "host"			=> array("string", false)
			, "database"		=> array("string", false)
			, "user"			=> array("string", false)
			, "password"		=> array("string", false)
			, "engine"			=> array("string", false)
			, "encoding"		=> array("string", false)
			, "create_db"		=> array("int", false)
			);
	}

	protected static $valid_engines = array(
		"innodb"
		,"myisam"
		,"galera");

	protected static $valid_encodings = array(
		"utf8_general_ci");

	protected static $valid_create_db = array(
		0
		,1);

	/**
	 * @inheritdocs
	 */
	protected function checkValueContent($key, $value) {
		switch($key) {
			case "encoding":
				return $this->checkContentValueInArray($value, self::$valid_encodings);
			case "engine":
				return $this->checkContentValueInArray($value, self::$valid_engines);
			case "create_db":
				return $this->checkContentValueInArray($value, self::$valid_create_db);
			case "host":
				return $this->checkContentHost($value);
			default:
				return parent::checkValueContent($key, $value);
		}
	}

	/**
	 * Check the host name to be valid
	 *
	 * @param $value
	 */
	protected function checkContentHost($value) {
		if(preg_match(self::IP_REGEX, strtolower($value))) {
			return true;
		}

		return (bool)preg_match(self::HOST_NAME_REGEX, strtolower($value));
	}
}