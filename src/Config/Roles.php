<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\ilse\Config;

/**
 * Configuration for Roles.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 *
 * @method array roles()
 */
class Roles extends Base {
	/**
	 * @inheritdocs
	 */
	public static function fields() {
		return array
			( "roles"			=> array(array("\\CaT\\ilse\\Config\\Role"), false)
			);
	}
}