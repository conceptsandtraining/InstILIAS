<?php
namespace CaT\InstILIAS\Config;

/**
 * Configuration for the tools required by ILIAS.
 *
 * @method string convert()
 * @method string zip()
 * @method string unzip()
 * @method string java()
 */
class Tools extends Base {
		/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			( "convert"	=> array("string", false)
			, "zip"		=> array("string", false)
			, "unzip"	=> array("string", false)
			, "java"	=> array("string", false)
			);
	}
}
