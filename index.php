<?php
require_once __DIR__ . '/vendor/autoload.php';

use IUT\Deefy\Dispatch\Dispatcher;

$dispatcher = new Dispatcher();
$dispatcher->run();
