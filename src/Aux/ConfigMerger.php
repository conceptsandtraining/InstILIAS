<?php

namespace CaT\Ilse\Aux;
use Symfony\Component\Yaml\Yaml as SYM;

/**
 * Merge any number of config files
 */
class ConfigMerger {
	public function __construct() {
	}

	/**
	 * @inheritdoc
	 */
	public function mergeConfigs(array $configs)
	{
		$ret = SYM::parse($configs[0]);

		if(count($configs) > 0) {
			for($i = 1; $i < count($configs); $i++) {
				$new = SYM::parse($configs[$i]);
				$ret = $this->addMissingKeys($ret, $new);

				foreach ($ret as $key => $value) {
					if(array_key_exists($key, $new)) {
						$ret[$key] = $this->merge($value, $new[$key]);
					}
				}
			}
		}

		return SYM::dump($ret);
	}

	/**
	 * Merge two arrays
	 *
	 * @param 	mixed 	$base
	 * @param 	mixed 	$new
	 * @return 	array
	 */
	protected function merge($base, $new)
	{
		if(is_array($base)) {
			$base = $this->addMissingKeys($base, $new);

			foreach ($base as $key => $value) {
				if(array_key_exists($key, $new)) {
					$base[$key] = $this->merge($value, $new[$key]);
				}
			}
		} else {
			if($base != $new) {
				$base = $new;
			}
		}

		return $base;
	}

	/**
	 * Add missing keys
	 *
	 * @param 	array 	$base
	 * @param 	array 	$new
	 * @return 	array
	 */
	protected function addMissingKeys(array $base, array $new)
	{
		$key_base = array_keys($base);
		$key_new = array_keys($new);
		$diff = array_diff($key_new, $key_base);

		if(count($diff) > 0) {
			foreach ($diff as $value) {
				$base[$value] = $new[$value];
			}
		}

		return $base;
	}
}
