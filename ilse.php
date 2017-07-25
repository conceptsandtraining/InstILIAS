#!/usr/bin/env php
<?php
/**
 * Main entry point for Ilse
 *
 * @author Daniel Weise 	<daniel.weise@concepts-and-training.de>
 */
require_once(__DIR__."/vendor/autoload.php");

// TODO: remove after development
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE);

$path = new \CaT\Ilse\CommonPathes();
$app = new \CaT\Ilse\App($path);
$app->run();