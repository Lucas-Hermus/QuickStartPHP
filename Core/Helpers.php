<?php

function path($relativePath){
    return __DIR__ . "/../" . $relativePath;
}

function jsonResponse($data, $httpStatus = 200) {
    http_response_code($httpStatus);
    header('Content-Type: application/json');
    echo json_encode($data);
    die();
}

function view($pathToFile){
    echo '<script src="/View/helpers.js"></script>';
    require path($pathToFile);
}

function test($pattern, $url) {
    // Extract variables from the pattern
    preg_match_all('/\{([^\/]+)\}/', $pattern, $matches);
    
    // Remove variables from the pattern
    $patternWithoutVariables = preg_replace('/\/\{[^\/]+\}/', '', $pattern);
    
    // Extract the same number of segments from the end of the URL
    $urlSegments = explode('/', rtrim($url, '/'));
    $variables = array_slice($urlSegments, -count($matches[1]));
    
    // Remove segments from the right side of the URL for each variable
    $urlWithoutVariables = implode('/', array_slice($urlSegments, 0, -count($matches[1])));
    
    // Return the result as an associative array
    return [
        'pattern_without_variables' => $patternWithoutVariables,
        'variables' => $variables,
        'url_without_variables' => $urlWithoutVariables,
    ];
}


function dd($data) {
    require path("Core/Assets/ddStyling.html");

    echo '<pre><code class="language-php">';
    echo nl2br(htmlspecialchars(var_export($data, true)));
    echo '</code></pre>';
    
    die();
}

function dump($data){
    require path("Core/Assets/ddStyling.html");

    echo '<pre><code class="language-php">';
    echo nl2br(htmlspecialchars(var_export($data, true)));
    echo '</code></pre>';
}
?>