# About 
---
QuickStartPHP is a lightweight and flexible PHP framework designed with the sole purpose of providing a learning experience. Created out of a passion for understanding the intricacies of web development frameworks, this project serves as my personal journey to dive deep into the underlying principles and mechanisms that power modern web applications.

Disclaimer: You might notice that the syntax choosen is very simmilar to Laravel syntax. This project has been heavily inspired by the way that Laravel handles sertain actions and I don't claim to have made someting completely unique.

# How to use
---
## Installation
As of the current version of QuickStartPHP installation is as easy as cloning the repository from this github.

## Routing
Routes can be declared in `routes.php` which is inside of the root directory.
next follow code examples on how to use each aspect of the routing system.

### Routing directly to a file

```php
// Routing directly to a file can be done with the Route class
Route::get('/', function () {  
    view('Pages/index.html');  
});
```

### Routing to a controller

```php
// Create a GET route of "/example" that routes to the function "index"
// inside of the ExampleController class
Route::get('/example', 'App\Controller\ExampleController@index');

// You may also choose a different http method
Route::post('/example', 'App\Controller\ExampleController@index');

// Groups can be created to simplify adding multiple routes under the same sub route
// The index function in the following example will be called when a POST request is 
// send to the route: "/api/example"
Route::group('/api', function () {
    Route::post('/example', 'App\Controller\ExampleController@index');
});

// Groups may be nested as many times as nessesary
Route::group('/api', function () {
  Route::group('/example', function () {
    Route::post('/route', 'App\Controller\ExampleController@index');
  });
});
```

### Route parameters

```php
// Anything inside {} will be seen as a route parameters
// In this example we create a route to /user/{anny-url-safe-string}
Route::post('/user/{id}', 'App\Controller\ExampleController@getUser');

// The controller function will recieve the parametrs:
// Note that the variable name does not necessarily have to be the same as in the route
// It is however important to keep the order the same
public function getUser($user_id){
 // Your code here...
}
```

## Model
### Creating a model
Creating a model is as easy as typing the following command:

```powershell
start command
```

After executing the command you can choose option `1`  and follow the prompts to create the model of your choosing.

```php
// the result will look something like this:
namespace App\src\Model;  
  
use App\Core\Database\Model;  
  
class Example extends Model  
{  
    public $table = "example";  
}
```

### Adding relations
Relations can be added by adding functions.
By using the previously created Model we can add a relation like this:
```php
class Example extends Model  
{  
    public $table = "example";  

	// first you specify the related class
	// after that you specify the local key
	// and lastly you specify the foreign key
	
	// one to one relation to a product class
	public function product(){
		return $this->hasOne(Product::class, "id", "user_id");
	}
	// one to many relation to a product class
	public function products(){
		return $this->hasMany(Product::class, "id", "user_id");
	}
}
```

## Database

### configuration
To configure your database credentials you can create a `.even.local` file inside of the root directory. In this file you can specify your database credentials among others.

```dotenv
# database 
DB_HOST=  
DB_USER=  
DB_PASSWORD= 
DB_NAME=
```

### Querying the database
To interact with data from the database you can use the following functions on your model.
For this example we wil use a `User` model. but any model will work.

#### Get
The get method triggers the database queries and returns the result as a array of objects.
Note that the get method will always return a array

```php
// get all records of a model:
$userData = User::get();

// to get a single record from the database you can use the first method:
$userData = User::first();

// to get a record by id you can use the find method
$userData = User::find(1)->first();

// here follow a few more ways in which you can filter the result:
// note that you still need to add either ->get() or ->first() 
// to actually retreve the data

// add a where clause where id (or anny field specified) must equel 3
User::where("id", 1);
// add manny where clauses
User::where(["id" => 1, "name" => "example"]);
// add manny where clauses while also providing a operator
User::where([["id", "<", 100], ["work_hours", ">" 10]]);

// you can sort records like this:
User::orderBy("work_hours");
// or sort by descending order:
User::orderByDesc("work_hours");

// you can limit the ammount of records you get by using the limit function
// in this example no more that 5 records will be retreved
User::limit(5);
```

#### Relations
To get records from the database with corresponding relations can be done using the `with` method.

```php
// in the following code example we get all users together with the sales of each user
$users = User::with('sales')->get();
// you can access the array of sales of each user with the object arrow ->
$salesOfFirstUser = $user[0]->sales;

// if you want more than one relation you can simply add more with spaces in between
$users = User::with('sales products')->get();

// want relations on relations? simply add a dot to indicate you want a relation of a // relation
$users = User::with('sales products.materials')->get();
// from this example you can access the queried relations like this:
$firstMaterial = $user[0]->products[0]->materials[0];

// Note that relatinos might not be a array depending on your deffinition of the relation
```

#### Insert
you can insert a new record by using the `insert` function and giving it a associative array with fields and values

```php
// insert a new user into the database
User::insert([
	"name" => "newUser",
	"work_hours" => 32,
]);

// if you want to know the id of your recently inserted record you can use inserGetId
$userId = User::insertGetId([
	"name" => "newUser",
	"work_hours" => 32,
]);
```

#### Update
To update a record you first have to use one of the previously mentioned filters to find your record.
Ater that you can use the `update` method the same way as how you would use `insert`

```php
// update user with id of 1
User::find(1)->update([
	"name" => "newName",
]);

// or update manny at the same time
// in this example all users with work hours of 41 will be updated to have 40 instead
User::where("work_hours", 41)->update([
	"work_hours" => 40,
]);
```

#### Delete
To delete a record you first have to find a record and than delete it using the `delete` method.

```php
// delete the user with a id of 1
User::find(1)->delete();

// or delete manny at the same time
// in this example all users with work hours of 41 will be deleted
User::where("work_hours", 41)->delete();
```

## Controller
The controller classes are classes that will handle all your requests.

### Setup
To create a controller you can type the following command in the terminal and select `2` in the selection screen:

```powershell
start command
```

After a the setup is complete you controller should look something like this:

```php
namespace App\src\Controller;

class ExampleController  
{  
	public function index(){        
		view("Pages/index.html");  
	}  
}
```

### Controller functions
There are a few helper functions that can be used inside the controller:

```php
class ExampleController  
{  
	public function index(){  
		// the view function returns a web page
		view("Pages/index.html");  
	}  
	public function users(){
		// you can also return a view with data
		// this will result in you having a superglobal javascript variable: $_DATA
		// this variable is set to whatever you pass into the view function's second 
		// argument
		$users = Users::get();
		view("Pages/index.html", $users);
	}

	public function getProducts(){
		//you can also return a json response for when you need a api route
		$users = Users::get();
		jsonResponse($users, 200);
	}
}
```
