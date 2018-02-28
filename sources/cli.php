<?php
require_once "Application/Core.php";

$argv = $_SERVER["argv"];

if (count($argv) > 0) {
	$core = new \Application\Core();
	$loader = $core->getLoader();
	$loader->load($argv[1]);
} else {
	echo "USE: cli.php %path/to/file%";
}


