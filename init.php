<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);

require_once __DIR__ . '/functions/template.php';
require_once __DIR__ . '/functions/helpers.php';
require_once __DIR__ . '/functions/validators.php';
require_once __DIR__ . '/functions/db.php';

session_start();

if (!file_exists(__DIR__ . '/config.php')) {
    exit('Файл конфигурации не найден');
}

$config = require_once __DIR__ . '/config.php';

$db = connectDB($config['db']);

$user = $_SESSION['user'] ?? null;
