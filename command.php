<?php
require __DIR__ .'/vendor/autoload.php';

use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$appplication = new Application();

$commands = require __DIR__ .'/app/commands.php';

foreach ($commands as $command):
    $appplication->add(new $command);
    endforeach;

$appplication->run();