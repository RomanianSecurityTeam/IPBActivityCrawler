<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

(new josegonzalez\Dotenv\Loader(dirname(__DIR__) . '/.env'))->parse()->toEnv();

function dd()
{
    echo '<pre>';

    foreach (func_get_args() as $arg) {
        print_r($arg);
    }

    die(PHP_EOL);
}

function match($regex, $source, $pos = 1) {
    preg_match($regex, $source, $result);

    return $result[$pos];
}

function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}
