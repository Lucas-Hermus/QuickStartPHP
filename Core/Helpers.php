<?php

function path($relativePath): string
{
    return __DIR__ . "/../" . $relativePath;
}

function jsonResponse($data, $httpStatus = 200) {
    http_response_code($httpStatus);
    header('Content-Type: application/json');
    echo json_encode($data);
    die();
}

function view($pathToFile, $data = array()){
    echo '<script src="/Public/Scripts/helpers.js"></script>';
    echo '<script>var _DATA = ' . json_encode($data) . ';</script>';
    require path("Public/" . $pathToFile);
}

function dd($data) {
    require path("Core/Packages/Highlight.js/index.html");

    echo '<pre><code class="language-php">';
    echo nl2br(htmlspecialchars(var_export($data, true)));
    echo '</code></pre>';
    
    die();
}

function dump($data){
    require path("Core/Packages/Highlight.js/index.html");

    echo '<pre><code class="language-php">';
    echo nl2br(htmlspecialchars(var_export($data, true)));
    echo '</code></pre>';
}

function is_assoc($array) {
    if (!is_array($array)) {
        return false;
    }

    $keys = array_keys($array);

    return count($keys) > 0 && array_keys($keys) !== $keys;
}