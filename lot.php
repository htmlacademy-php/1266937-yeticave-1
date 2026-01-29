<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    http_response_code(404);
    exit('Некорректный идентификатор лота');
}

$lot = getLotById($db, $id);

if ($lot === null) {
    http_response_code(404);
    die("Лот не найден");
}

$navContent = includeTemplate(
    'nav.php',
    [
        'categories' => $categories
    ]
);

$pageContent = includeTemplate(
    'lot.php',
    [
        'nav' => $navContent,
        'lot' => $lot,
        'user' => $user
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'content' => $pageContent,
        'nav' => $navContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - ' . $lot['title']
    ]
);

print ($layoutContent);
