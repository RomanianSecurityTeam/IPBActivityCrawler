<?php

require_once 'helpers.php';

$capsule = new Illuminate\Database\Capsule\Manager;

// Initialize the database connection.
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => env('DB_HOST'),
    'database'  => env('DB_NAME'),
    'username'  => env('DB_USER'),
    'password'  => env('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
]);

$capsule->setAsGlobal();