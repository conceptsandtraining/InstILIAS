<?php
/**
 * Main entry point for Ilse
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
require_once(__DIR__."/vendor/autoload.php");


error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE && ~E_WARNING);

$checker 	= new \CaT\Ilse\IliasRequirementChecker();
$merger 	= new \CaT\Ilse\MergeConfigs();
$path 		= new \CaT\Ilse\Pathes();
$git 		= new \CaT\Ilse\GitExecutor();
$parser 	= new \CaT\Ilse\YamlParser();
$gw 		= new \CaT\Ilse\Git\GitWrapper();
$app 		= new \CaT\Ilse\App($path, $merger, $checker, $git, $parser, $gw);

// Do not run in unit testing context
if (stripos($_SERVER["SCRIPT_NAME"], "phpunit") === false) {
	$app->run();
}

