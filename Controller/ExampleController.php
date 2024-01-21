<?php

namespace App\Controller;

class ExampleController
{
    public function __construct()
    {

    }

    public function index()
    {
        return view('View/Pages/examplePage.html');
    }

    public function test(){

        return view('View/Pages/monkey.html');
    }

    public function apiTest(){
        $test = [
            "monkey" => 3,
            "state" => false
        ];
        return jsonResponse($test, 200);
    }

    public function displayInput($test, $test2){
        dump($test);
        dump($test2);
        return view('View/Pages/monkey.html');
    }

}

?>