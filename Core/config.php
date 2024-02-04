<?php

$_ENV = parseEnv();


require path("Core/autoloader.php");
spl_autoload_register('initAutoloader');
use App\Core\Routing\Router;
use App\Core\Routing\Route;
$router = new Router();
Route::setRouter($router);
require_once path("routes.php");
$router->handleRequest($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);


function parseEnv(): array
{
    $lines = file(path(".env.local"), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $config = [];
    foreach ($lines as $line) {
        // Ignore comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2) + [NULL, NULL];
        if ($key !== NULL && $value !== NULL) {
            $config[trim($key)] = trim($value);
        }
    }

    return $config;
}

?>