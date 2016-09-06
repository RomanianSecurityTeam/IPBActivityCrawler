<?php


require_once 'includes/config.php';

if (isset($_GET['lastId'])) {
    (new Feedr\API)->getData($_GET['lastId']);
}

die('[]');