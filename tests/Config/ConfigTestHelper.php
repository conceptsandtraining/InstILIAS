<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

trait ConfigTestHelper {
	public function buildProviderCombinations($provider_names) {
		$providers = [];
		$defaults = [];

		foreach ($provider_names as $provider) {
			$providers[$provider] = $this->$provider();
			$defaults[$provider] = $providers[$provider][0];
			if (!$defaults[$provider][1]) {
				throw new \LogicException("Problem in test: First provided value should be ok.");
			}
		}

		foreach ($provider_names as $current_provider) {
			foreach ($providers[$current_provider] as $val) {
				$vals = [];
				$is_ok = true;
				foreach ($provider_names as $provider) {
					if ($current_provider == $provider) {
						$vals[] = $val[0];
						$is_ok = $is_ok && $val[1];
					}
					else {
						$vals[] = $defaults[$provider][0];
						$is_ok = $is_ok && $defaults[$provider][1];
					}
				}
				$vals[] = $is_ok;
				yield $vals;
			}
		}
	}
}
