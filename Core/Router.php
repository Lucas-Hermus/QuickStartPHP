<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $currentGroup = '';
    private $viewDir = '/../View/';
    
    public function group($prefix, $callback)
    {
        $previousGroup = $this->currentGroup;
        $this->currentGroup .= $prefix;

        $callback($this);

        $this->currentGroup = $previousGroup;
    }

    public function addRoute($uri, $handler)
    {
        $uri = $this->currentGroup . $uri;
        $this->routes[$uri] = $handler;
    }

    public function handleRequest($request)
    {
        if (array_key_exists($request, $this->routes)) {
            $handler = $this->routes[$request];

            if (is_callable($handler)) {
                // If the handler is a callable function, execute it
                $handler();
            } else {
                // If the handler is a controller class, create an instance and call a default method (e.g., 'index')
                $parts = explode('@', $handler);
                $controller = new $parts[0]();
                $method = $parts[1] ?? 'index';

                if (method_exists($controller, $method)) {
                    $controller->$method();
                } else {
                    http_response_code(404);
                    require __DIR__ . $this->viewDir . '404.php';
                }
            }
        } else {
            http_response_code(404);
            require __DIR__ . $this->viewDir . '../View/404.php';
        }
    }
}
?>