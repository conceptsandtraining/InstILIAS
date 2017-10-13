<?php
namespace CaT\Ilse\Aux;

use Symfony\Component\Yaml\Yaml as SYM;

/**
 * Class YamlParser
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
class YamlParser implements Yaml
{
	/**
	 * @inheritdoc
	 */
	public function parse($content)
	{
		assert('is_string($filename)');
		return SYM::parse($content);
	}
}