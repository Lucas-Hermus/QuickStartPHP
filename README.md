# About 
---
QuickStartPHP is a lightweight and flexible PHP framework designed with the sole purpose of providing a learning experience. Created out of a passion for understanding the intricacies of web development frameworks, this project serves as my personal journey to dive deep into the underlying principles and mechanisms that power modern web applications.

# How to use
---
## Installation
As of the current version of QuickStartPHP installation is as easy as cloning the repository from this github.

## Routing
Routes can be declared in `routes.php` which is inside of the root directory.
next follow code examples on how to use each aspect of the routing system.
#### Including a controller

```php
// Include your controller by specifying the path.
require_once path("Controller/YourController.php");
```

### Routing directly to a file

```php
// Routing directly to a file can be done with the build in router class
$router->addRoute('/', function () {
    return view('View/example.html');
});
```

### Routing to a controller

```php
// Create a GET route of "/example" that routes to the function "index"
// inside of the ExampleController class
$router->addRoute('/example', 'App\Controller\ExampleController@index');

// You may also specify the HTTP method (the default is "GET")
$router->addRoute('/example', 'App\Controller\ExampleController@index', "POST");

// Groups can be created to simplify adding multiple routes under the same sub route
// The index function in the following example will be called when a POST request is 
// send to the route: "/api/example"
$router->group('/api', function ($router) {
    $router->addRoute('/example', 'App\Controller\ExampleController@index', "POST");
});

// Groups may be nested as many times as nessesary
$router->group('/api', function ($router) {
  $router->group('/example', function ($router) {
    $router->addRoute('/route', 'App\Controller\ExampleController@index', "POST");
  });
});
```

### Route parameters

```php
// Anything inside {} will be seen as a route parameters
// In this example we create a route to /user/{anny-url-safe-string}
$router->addRoute('/user/{id}', 'App\Controller\ExampleController@getUser', "POST");

// The controller function will recieve the parametrs:
// Note that the variable name does not necessarily have to be the same as in the route
// It is however important to keep the order the same
public function getUser($user_id){
 // Your code here...
}
```

