<?php

namespace App;

require "./Core/Router.php";

use App\Core\Router;

require_once __DIR__ . '/Controller/ExampleController.php';

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$viewDir = '/View/';

$router = new Router();

$router->addRoute('/', function () use ($viewDir) {
    return view('View/Pages/home.html');
});


$router->group('/example', function ($router) {
    $router->addRoute('', 'App\Controller\ExampleController@index');
    $router->addRoute('/show/{test}/{k}', 'App\Controller\ExampleController@displayInput');
    $router->addRoute('/show/{test}/{k}', 'App\Controller\ExampleController@displayInput');
});


$router->group('/api', function ($router){
    $router->addRoute('/test', 'App\Controller\ExampleController@apiTest', 'POST');
});

$router->handleRequest($request, $method);
?>