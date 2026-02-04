<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lots = getLots($db);

$navContent = includeTemplate(
    'nav.php',
    ['categories' => $categories]
);

$pageContent = includeTemplate(
    'main.php',
    [
        'nav' => $navContent,
        'categories' => $categories,
        'lots' => $lots,
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'nav' => $navContent,
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - Главная страница'
    ]
);

print ($layoutContent);
