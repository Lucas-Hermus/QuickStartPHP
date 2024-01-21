<?php

$_ENV = parseEnv();

function parseEnv()
{
    $lines = file(path(".env.local"), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $config = [];
    foreach ($lines as $line) {
        // Ignore comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2) + [NULL, NULL];
        if ($key !== NULL && $value !== NULL) {
            $config[trim($key)] = trim($value);
        }
    }

    return $config;
}

?>