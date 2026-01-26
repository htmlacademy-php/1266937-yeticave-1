<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lots = getLots($db);

$pageContent = includeTemplate(
    'main.php',
    [
        'categories' => $categories,
        'lots' => $lots
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - Главная страница'
    ]
);

print ($layoutContent);
