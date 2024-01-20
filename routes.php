<?php

namespace App;

require "./Core/Router.php";

use App\Core\Router;

require __DIR__ . '/Controller/ExampleController.php';

$request = $_SERVER['REQUEST_URI'];
$viewDir = '/View/';

$router = new Router();

$router->addRoute('/', function () use ($viewDir) {
    require __DIR__ . $viewDir . 'home.html';
});

$router->group('/example', function ($router) {
    $router->addRoute('', 'App\Controller\ExampleController@index');
});


$router->handleRequest($request);

?>
