<?php
namespace CaT\Ilse\Aux;

/**
 * Interface Yaml
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
interface Yaml
{
	/**
	 * Parse a yaml string
	 *
	 * @param 	string 	$content
	 * @return 	string[]
	 */
	public function parse($content);
}