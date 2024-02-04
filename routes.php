<?php
use App\Core\Routing\Route;

Route::get('/', function () {
    view('Pages/index.html');
});

Route::get('/test', 'App\src\Controller\TestController@index');
