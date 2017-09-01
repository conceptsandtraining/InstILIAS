<?php

namespace CaT\Ilse\Configurators;

/**
 * Configure ILIAS learning progress part
 * 
 * Create roles
 */
class LearningProgress {
	/**
	 * @var \ilSetting
	 */
	protected $gSetting;

	public function __construct($absolute_path, \ilSetting $settings) {
		$this->gSetting = $settings;
	}

	/**
	 * Configure LP
	 *
	 * @param \CaT\Ilse\Config\LearningProgress $lp
	 */
	public function learningProgress(\CaT\Ilse\Config\LearningProgress $lp) {
		$this->gSetting->set("enable_tracking", $lp->enabled());
		$this->gSetting->set("save_user_related_data", !$lp->anonym());
		$this->gSetting->set("tracking_time_span",$lp->timeSpan());
		$this->gSetting->set("lp_extended_data", $lp->extendedData());
		$this->gSetting->set("object_statistics", $lp->objectStatistics());
		$this->gSetting->set("lp_learner", $lp->ownLp());
		$this->gSetting->set("session_statistics", $lp->sessionStatistics());
		$this->gSetting->set("lp_list_gui", $lp->personalDesktop());
	}
}