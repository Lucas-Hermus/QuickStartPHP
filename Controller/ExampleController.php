<?php

namespace App\Controller;

class ExampleController
{
    public function __construct()
    {

    }

    public function index()
    {
        require path('view', '/examplePage.html');
    }
}

?>