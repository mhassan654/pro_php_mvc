<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new Framework\Routing\Router();

// we expect the routes file to return a callable
// or else this code would break

$routes = require_once __DIR__ . '/../app/routes.php';
$routes($router);

try {
    print $router->dispatch();
} catch (Throwable $e) {
}
