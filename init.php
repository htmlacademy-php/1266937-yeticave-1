<?php

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);

require_once __DIR__ . '/functions/template.php';
require_once __DIR__ . '/functions/validators.php';
require_once __DIR__ . '/functions/db.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();

if (!file_exists(__DIR__ . '/config.php')) {
    exit('Файл конфигурации не найден');
}

$config = require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = connectDB($config['db']);

date_default_timezone_set('Europe/Moscow');
mysqli_query($db, "SET time_zone = '+03:00'");

$user = $_SESSION['user'] ?? null;
