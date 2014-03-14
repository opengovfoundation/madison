<?php
require realpath(dirname(__FILE__)) . '/../../vendor/apix/autoloader/src/php/Apix/Autoloader.php';

$loader = new Apix\Autoloader;
$loader->prepend(realpath(dirname(__FILE__)). '/../modules');
$loader->register(true);