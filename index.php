<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/data.php';

$pageContent = includeTemplate('main.php', ['categories' => $categories, 'lots' => $lots]);
$layoutContent = includeTemplate('layout.php', [
    'content' => $pageContent,
    'categories' => $categories,
    'isAuth' => $isAuth,
    'userName' => $userName,
    'title' => 'YetiCave - Главная страница'
]);

print ($layoutContent);
