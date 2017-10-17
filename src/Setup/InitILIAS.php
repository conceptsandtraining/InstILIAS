<?php
namespace CaT\Ilse\Setup;

/**
 * Interface InitILIAS is for classe which depends on an initialized ILIAS.
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
interface InitILIAS
{
	/**
	 * 
	 */
	public function initILIASIsNotInitialized();
}
