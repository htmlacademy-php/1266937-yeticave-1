<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lots = [];

$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$search = $search ? trim($search) : '';

if ($search !== '') {
    $lots = getLotsViaSearch($db, $search);
}

$pageContent = includeTemplate(
    'search.php',
    [
        'categories' => $categories,
        'search' => $search,
        'lots' => $lots,
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'Результаты поиска по запросу: ' . $search
    ]
);

print ($layoutContent);
