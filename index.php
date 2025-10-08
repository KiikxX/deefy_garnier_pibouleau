<?php

require_once __DIR__ . '/vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


use IUT\Deefy\Dispatch\Dispatcher;

$dispatcher = new Dispatcher();
$dispatcher->run();
