<?php
use App\Core\Routing\Route;

Route::get('/', function () {
    view('Pages/index.html');
});
