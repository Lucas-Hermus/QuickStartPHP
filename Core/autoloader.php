<?php
function initAutoloader($className)
{
    $file = __DIR__ . '/../' . classNameToFilePath($className);
    if (file_exists($file)) {
        include $file;
    }
}

function classNameToFilePath($inputString) {
    // Replace double backslashes with a single forward slash
    $transformedString = str_replace("\\", "/", $inputString);

    // Remove the initial "App/" and append ".php"
    $transformedString = str_replace("App/", "", $transformedString) . ".php";

    return $transformedString;
}