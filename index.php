<?php


require_once 'includes/config.php';

if (isset($_GET['lastId'])) {
    (new Feedr\API)->getData($_GET['lastId']);
}

if (count($argv) && isset($argv[1]) && $argv[1] == 'run') {
    (new Feedr\API)->parseActivity();
    die('Done parsing');
}

die('[]');