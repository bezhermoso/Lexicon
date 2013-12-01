<?php

$autoloader = __DIR__ . '/../vendor/autoload.php';

if (!is_file($autoloader)) {

    trigger_error($autoloader . ' not found. Run `composer install`.', E_ERROR);
    die();
}

require $autoloader;
