<?php
require_once __DIR__ .'/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ .'/..');
$dotenv->load();

$router = new Framework\Routing\Router();

// we expect the routes file to reetu a caalable
//or else this code woruld break
$routes = require_once __DIR__.'/../app/routes.php';
$routes($router);

try {
    print $router->dispatch();
} catch (Throwable $e) {
}
