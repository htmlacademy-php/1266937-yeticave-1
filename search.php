<?php

/**
 * @var mysqli $db
 * @var array $user
 * @var array $pagination
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lots = [];
$categoryName = '';

$search = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$categoryId = (int) ($_GET['id'] ?? 0);

$pageItems = 9;
$itemsCount = 0;
$pagesCount = 1;

if ($search !== '') {
    $itemsCount = getItemsCount($db, $search);

    require_once 'pagination.php';
    $lots = findLotsBySearch($db, $search, $pageItems, $offset);

} else if ($categoryId > 0) {
    foreach ($categories as $category) {
        if ((int) $category['id'] === $categoryId) {
            $categoryName = $category['title'];
            break;
        }
    }

    $itemsCount = getItemsCount($db, null, $categoryId);

    require_once 'pagination.php';
    $lots = findLotsbyCategory($db, $categoryId, $pageItems, $offset);
} else {
    $pagination = ['pagesCount' => 0];
}

$navContent = includeTemplate(
    'nav.php',
    ['categories' => $categories]
);

$pageContent = includeTemplate(
    'search.php',
    [
        'nav' => $navContent,
        'search' => $search,
        'lots' => $lots,
        'categoryName' => $categoryName,
        'pagination' => $pagination
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'nav' => $navContent,
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'Результаты поиска по запросу: ' . $search
    ]
);

print ($layoutContent);
