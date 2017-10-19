<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Aux;
use Symfony\Component\Yaml\Yaml as SYM;

/**
 * implementation of a parser
 * used configuration language is yaml
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 */
class YamlConfigParser implements ConfigParser {
	/**
	 * @inheritdoc
	 */
	public function read_config($string, $class) {
		assert('is_string($string)');
		assert('is_string($class)');

		if(!class_exists($class, true)){
			throw new \LogicException("Class '$class' does not exists");
		}

		$yaml = SYM::parse($string);

		return $this->createConfig($yaml, $class);
	}

	/**
	 * creates an instance of requested class in $class
	 *
	 * @param array $yaml
	 * @param string $class
	 * @param string $path
	 *
	 * @throws LogicException
	 *
	 * @return instance of $class
	 */
	protected function createConfig(array $yaml, $class, $path = "") {
		assert('is_string($class)');

		$vals = array();
		foreach ($class::fields() as $key => $type) {
			$type_val = $type[0];
			$optional = $type[1];

			if(is_subclass_of($type_val, "\\CaT\\Ilse\\Config\\Base") ) {
				$value = $this->yamlValue($yaml, $key, $path, $optional);

				if(!$optional || ($optional && $value !== null)) {
					$new_path = ($path == "") ? $key : $path.":".$key;
					$vals[] = $this->createConfig($value, $type_val, $new_path);
				} else {
					$vals[] = null;
				}
			}
			else if ($type_val == "string") {
				$vals[] = $this->yamlValue($yaml, $key, $path, $optional, "");
			}
			else if ($type_val == "int") {
				$vals[] = $this->yamlValue($yaml, $key, $path, $optional, 0);
			}
			else if(is_array($type_val)) {
				assert('count($type_val) === 1');
				$content = $type_val[0];
				
				if(is_subclass_of($content, "\\CaT\\Ilse\\Config\\Base")) {
					$sub_vals = array();
					$new_path = ($path == "") ? $key : $path.":".$key;
					foreach ($this->yamlValue($yaml, $key, $new_path, $optional, array()) as $key => $value) {
						$new_path2 = ($new_path == "") ? $key : $new_path.":".$key;
						$sub_vals[] = $this->createConfig($value, $content, $new_path2);
					}
					$vals[] = $sub_vals;
				} else {
					$vals[] = $this->yamlValue($yaml, $key, $path, $optional, array());
				}
			}
			else {
				throw new \LogicException("Unknown Type: ".$type_val);
			}
		}

		//reflection class is need to be used, because the config constructor do not accept array as param
		//ReflectionClass::newInstanceArgs splits the array into single vars and forward them to the class constructor
		$class_handle = new \ReflectionClass($class);
		return $class_handle->newInstanceArgs($vals);
	}

	/**
	 * get the search value from yaml parsed array
	 *
	 * @param array $yaml
	 * @param string|integer $key
	 * @param string $path
	 * @param boolean $optional
	 * @param string|integer|array|null $baseValue
	 *
	 * @return string|integer|array|null
	 */
	protected function yamlValue(array $yaml, $key, $path, $optional, $baseValue = null) {
		if(!array_key_exists($key, $yaml) && $optional) {
			return $baseValue;
		} else if(!array_key_exists($key, $yaml) && !$optional) {
			throw new \InvalidArgumentException("Required configuration entry \"".$path.":".$key."\" was not found. Please check your config.yaml");
		}

		return $yaml[$key];
	}

	/**
	 * Read a yaml file
	 * 
	 * @param string 	$path
	 * @return array 	'name' => data
	 */
	public function read($path)
	{
		assert('is_string($path)');

		try
		{
			$yaml_array = SYM::parse(file_get_contents($path));
		}
		catch (ParseException $e)
		{
			printf("Unable to parse the YAML string %s", $e->getMessage());
			throw $e;
		}
		return $yaml_array;
	}

	/**
	 * Convert an array to yaml
	 * 
	 * @param array 	'name' => data
	 * @return string
	 */
	public function arr2yaml(array $data)
	{
		return SYM::dump($data);
	}
}
