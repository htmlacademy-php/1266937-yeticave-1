<?php

/**
 * @var int $isAuth
 * @var string $userName
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

$pageContent = includeTemplate(
    'lot.php',
    [
        'lot' => $lot,
        'categories' => $categories
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'content' => $pageContent,
        'categories' => $categories,
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'YetiCave - ' . $lot['title']
    ]
);

print ($layoutContent);
