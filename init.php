<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);

require_once __DIR__ . '/functions/template.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/validators.php';
require_once __DIR__ . '/functions/db.php';

$config = require_once __DIR__ . '/config.php';

$db = connectDB($config['db']);

$isAuth = rand(0, 1);
$userName = 'Angelina';
