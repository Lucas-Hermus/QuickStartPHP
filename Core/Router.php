<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $currentGroup = '';
    
    public function group($prefix, $callback)
    {
        $previousGroup = $this->currentGroup;
        $this->currentGroup .= $prefix;

        $callback($this);

        $this->currentGroup = $previousGroup;
    }

    public function addRoute($uri, $handler, $method = 'GET')
    {
        $uri = $this->currentGroup . $uri;
        $this->routes[] = [
            'uri' => $uri,
            'handler' => $handler,
            'method' => strtoupper($method)
        ];
    }

    public function handleRequest($request, $method)
    {
        foreach ($this->routes as $route) {
            $variables = $this->getVariables($route['uri'], $request);
            
            if (($route['uri'] === $request || !empty($variables)) && $route['method'] === $method) {
                $handler = $route['handler'];

                if (is_callable($handler)) {
                    // If the handler is a callable function, execute it
                    $handler();
                } else {
                    // If the handler is a controller class, create an instance and call a default method (e.g., 'index')
                    $parts = explode('@', $handler);
                    $controller = new $parts[0]();
                    $method = $parts[1] ?? 'index';

                    if (method_exists($controller, $method)) {
                        if (!empty($variables)){
                            $controller->$method(...$variables);
                            return;
                        }
                        $controller->$method();
                    } else {
                        http_response_code(404);
                        return view("View/404.html");
                    }
                }

                return;
            }
        }

        // If no matching route is found
        http_response_code(404);
        return view("View/404.html");
    }

    private function getVariables($pattern, $url) {
        // Extract variables from the pattern
        preg_match_all('/\{([^\/]+)\}/', $pattern, $matches);
        
        // Remove variables from the pattern
        $patternWithoutVariables = preg_replace('/\/\{[^\/]+\}/', '', $pattern);
        
        // Extract the same number of segments from the end of the URL
        $urlSegments = explode('/', rtrim($url, '/'));
        $variables = array_slice($urlSegments, -count($matches[1]));
        
        // Remove segments from the right side of the URL for each variable
        $urlWithoutVariables = implode('/', array_slice($urlSegments, 0, -count($matches[1])));
        
        if ($patternWithoutVariables != $urlWithoutVariables){ return []; }
        return $variables;
    }
}
?>
