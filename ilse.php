<?php
/**
 * Main entry point for Ilse
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
require_once(__DIR__."/vendor/autoload.php");

$app 		= new \CaT\Ilse\App\App();
//
// Do not run in unit testing context
if (stripos($_SERVER["SCRIPT_NAME"], "phpunit") === false) {
	$app->run();
}

