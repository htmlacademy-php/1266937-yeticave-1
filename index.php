<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/getwinner.php';

$categories = getCategories($db);
$lots = getLots($db);

$navContent = includeTemplate(
    'nav.php',
    ['categories' => $categories]
);

$allLots = includeTemplate(
    'all-lots.php',
    ['lots' => $lots]
);

$pageContent = includeTemplate(
    'main.php',
    [
        'categories' => $categories,
        'lots' => $lots,
        'allLots' => $allLots
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
