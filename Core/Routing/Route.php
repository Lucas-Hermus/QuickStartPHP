<?php

namespace App\Core\Routing;

class Route
{
    private static $routerInstance;

    public static function setRouter($router){
        static::$routerInstance = $router;
    }

    public static function __callStatic($method, $args)
    {
        return static::$routerInstance->$method(...$args);
    }
}