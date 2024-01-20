<?php

function path($type, $route, $depth = 1){
    $path = "";

    switch ($type){
        case "view":
            $path = "./View";
        break;
        default:
            $path = "./View";
        break;
    }

    $path = str_repeat('.', $depth) . $path;

    return __DIR__ . "/" . $path . $route;
}

?>