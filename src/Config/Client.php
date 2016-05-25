<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\InstILIAS\Config;

/**
 * Configuration for one client of ILIAS.
 *
 * @method string dataDir()
 * @method string name()
 * @method string passwordEncoder()
 */
class Client extends Base {
	/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			( "data_dir"			=> array("string", false)
			, "name"				=> array("string", false)
			, "password_encoder"	=> array("string", false)
			);
	}

	protected static $valid_password_encoders = array
			( "md5"
			, "bcrypt"
			);

	/**
	 * @inheritdocs
	 */
	protected function checkValueContent($key, $value) {
		switch($key) {
			case "password_encoder":
				return $this->checkContentValueInArray($value, self::$valid_password_encoders);
			default:
				return parent::checkValueContent($key, $value);
		}
	}
}
