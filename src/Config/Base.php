<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Config;

/**
 * Base class for all configs.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 */
abstract class Base {
	/**
	 * Get all fields this config defines and their types.
	 *
	 * @return	array		array($name	=> ConfigClass|"string"|"int"|array(ConfigClass|"string"|"int"), optional => false|true)
	 */
	public static function fields() {
		return array();
	}

	/**
	 * Create a config object, pass parameters according to getFields of the
	 * concrete class.
	 */
	final public function __construct() {
		$params = func_get_args();
		$this->checkParams($params);
		$this->fillProperties($params);
	}

	/**
	* Return the value of the called property. Substitution for property getter
	*
	* @param sring $name  	name of called function
	* @param array $params 	forwarded params
	*
	* @throws BadMethodCallException if no property is available for called $name
	*
	* @return mixed Value of called property
	*/
	final public function __call($name, $params) {
		assert('count($params) === 0');
		$name = $this->from_camel_case($name);
		if (!array_key_exists($name, $this->fields())) {
			throw new \BadMethodCallException
						("Could not call unknown getter for field '$name'");
		}
		return $this->$name;
	}

	/**
	 * Change camel case to underscore splittet value
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	private function from_camel_case($name) {
		return preg_replace_callback("/[A-Z]/", function ($matches) {
			return "_".strtolower($matches[0]);
		}, $name);
	}

	/**
	 * are there enough values in $params
	 *
	 * @param array 	$params 	values to fill the config
	 * @throws InvalidArgumentException
	 */
	private function checkParams($params) {
		$fields = $this->fields();
		$amount_pars = count($params);
		if ($amount_pars !== count($fields)) {
			throw new \InvalidArgumentException
						("Only got $amount_pars parameters, expected: "
						.implode(", ", array_keys($fields))
						);
		}
	}

	/**
	 *
	 * @param array 	$params 	values to fill the config 
	 */
	private function fillProperties($params) {
		$fields = $this->fields();
		foreach ($fields as $key => $type) {
			$this->fillProperty($key, $type, array_shift($params));
		}
	}

	/**
	 * fill property by key 
	 *
	 * @param string 			$key 	property key
	 * @param string 			$type 	expected input type
	 * @param string|int|array 	$value 	entered value
	 *
	 * @throws InvalidArgumentException
	 */
	protected function fillProperty($key, $type, $value) {
		$this->checkValue($key, $type[0], $value, $type[1]);
		$this->$key = $value;
	}

	/**
	* Checks given value is type of needed.
	*
	* @param string|integer 		$key
	* @param string|integer|array 	$type
	* @param mixed 					$value
	* @param bool 					$optional
	*
	* @throws InvalidArgumentException if value is not of needed type
	*/
	private function checkValue($key, $type, $value, $optional) {
		if ($type == "string") {
			$ok = is_string($value);
		}
		else if ($type == "int") {
			$ok = is_int($value);
		}
		else if (is_array($type)) {
			$ok = $this->checkArray($key, $type, $value, $optional);
		}
		else {
			assert('is_subclass_of($type, "\\CaT\\Ilse\\Config\\Base")');
			$ok = $value instanceof $type;
		}

		if ($ok and $key !== null) {
			$ok = $this->checkValueContent($key, $value);
		}

		if (!$ok) {
			throw new \InvalidArgumentException
						( "Error in field $key: Expected "
						. print_r($type, true)." found ".print_r($value, true));
		}
	}

	/**
	 * values in array are from $type
	 *
	 * @param string|integer 		$key
	 * @param string|integer|array 	$type
	 * @param mixed 				$value
	 * @param bool 					$optional
	 *
	 * @return boolean
	 */
	protected function checkArray($key, $type, $value, $optional) {
		assert('count($type) === 1');
		$content = $type[0];
		if (!is_array($value)) {
			return false;
		}
		else {
			if(count($value) == 0){
				return true;
			} else {
				try {
					// TODO: This is not very nice. I introduced $key to make
					// it possible for concrete config classes to perform further
					// checks on input values, but i would call checkValue with a
					// specific key with array($type) and $type as well.
					foreach ($value as $v) {
						$this->checkValue(null, $content, $v, $optional);
					}
					return true;
				}
				catch (\InvalidArgumentException $e) {
					return false;
				}
			}
		}
	}

	/**
	 * is entered value for the key valid
	 *
	 * @param string 				$key 	key of value should be set
	 * @param string|int|array 		$value 	entered value
	 *
	 * @return bool
	 */
	protected function checkValueContent($key, $value) {
		return true;
	}

	/**
	 * is value in array
	 *
	 * TODO: remove this and just use "in_array"
	 *
	 * @param string|int 	$value 		entered value
	 * @param array 		$valids		valid entries for the key
	 *
	 * @return bool
	 */
	final protected function checkContentValueInArray($value, array $valids) {
		return in_array($value, $valids);
	}

	/**
	 * is value in correct format
	 *
	 * @param string 		$value 		entered value
	 * @param string 		$preg 		pregmatch for value
	 *
	 * @return bool
	 */
	final protected function checkContentPregmatch($value, $preg) {
		return (bool)preg_match($preg, strtolower($value));
	}
}
