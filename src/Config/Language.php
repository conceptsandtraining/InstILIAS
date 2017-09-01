<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Config;

/**
 * Config for the languages to be used in ILIAS.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 *
 * @method string defaultLang()
 * @method array toInstallLangs()
 */
class Language extends Base {
	/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			( "default"			=> array("string", false)
			, "available"		=> array(array("string"), false)
			);
	}

	protected static $valid_languages = array(
		"ar"
		,"bg"
		,"cs"
		,"da"
		,"de"
		,"el"
		,"en"
		,"es"
		,"et"
		,"fa"
		,"fr"
		,"hu"
		,"it"
		,"ja"
		,"ka"
		,"lt"
		,"nl"
		,"pl"
		,"pt"
		,"ro"
		,"ru"
		,"sk"
		,"sq"
		,"sr"
		,"tr"
		,"uk"
		,"vi"
		,"zh");

	/**
	 * @inheritdocs
	 */
	protected function checkValueContent($key, $value) {
		switch($key) {
			case "available":
				if (count($value) == 0) {
					return false;
				}
				foreach ($value as $lang) {
					if (!$this->checkContentValueInArray($lang, self::$valid_languages)) {
						return false;
					}
				}
				return true;
			case "default":
				return $this->checkContentValueInArray($value, self::$valid_languages);
			default:
				return parent::checkValueContent($key, $value);
		}
	}
}
