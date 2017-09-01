<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS certificate part
 * 
 * Create roles
 */
class Certificates {
	public function __construct($absolute_path) {
		require_once($absolute_path."/Services/Administration/classes/class.ilSetting.php");
	}

	/**
	 * Enable or disable certifcates
	 *
	 * @param \CaT\Ilse\Config\Certificate $certificate
	 */
	public function certificate(\CaT\Ilse\Config\Certificate $certificate) {
		$certificate_settings = new \ilSetting("certificate");
		$certificate_settings->set("active", $certificate->enable());
	}
}