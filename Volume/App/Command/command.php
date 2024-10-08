<?php

use App\Command\Commander;
use Core\App;

$path = dirname(__DIR__, 2);
include_once ($path . '/autoloader.php');

App::initConfigs();
App::initDb();
App::initApp();

// example php command.php -migrate 

if (array_key_exists('m', getopt("m:"))) {
    $obj = new Commander;
    $obj->runMigration();
}