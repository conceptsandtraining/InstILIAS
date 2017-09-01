<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Config;

/**
 * Configuration for server values
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 *
 * @method string masterPassword()
 */
class Setup extends Base {
	/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			("master_password"	=> array("string", false),
			 "user"				=> array("string", false),
			 "group"			=> array("string", false)
			);
	}
}