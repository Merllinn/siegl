<?php

// if our site is temporary off.. then set TRUE
$maintenanceMode = FALSE;
$local = false;
if($_SERVER["HTTP_HOST"]=="www.siegl.tp"){
	$local = true;
}


if ($maintenanceMode) {
    // Safemode
    require '.maintenance.php';
} else {
    ini_set("use_strict_mode", false);

    define('BASE_DIR', realpath(__DIR__));
    define('APP_DIR', realpath(__DIR__ . '/app'));
    define('DATA_DIR', realpath(__DIR__ . '/data'));

	define('FOLDER', "");

	$container = require __DIR__ . '/app/bootstrap.php';

	$container->getByType(Nette\Application\Application::class)
		->run();
}