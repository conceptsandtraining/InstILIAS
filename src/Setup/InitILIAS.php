<?php
namespace CaT\Ilse\Setup;

/**
 * Interface InitILIAS is for classes which depends on an initialized ILIAS.
 *
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 * @copyright Extended GPL, see LICENSE
 */
interface InitILIAS
{
	/**
	 * Checks wheter ILIAS is already initialized.
	 * If not, initialize ILIAS.
	 *
	 * @return void
	 */
	public function initILIASIsNotInitialized();
}
