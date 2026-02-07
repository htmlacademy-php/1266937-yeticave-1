<?php

/**
 * @var mysqli $db
 * @var array $user
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lots = [];
$categoryName = '';

$search = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$categoryId = (int) ($_GET['id'] ?? 0);

$pageItems = 9;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($currentPage - 1) * $pageItems;

$pagination = '';
$title = '';

if ($search !== '') {
    $itemsCount = getItemsCount($db, $search);
    $lots = findLotsBySearch($db, $search, $pageItems, $offset);
    $pagination = getPaginationTemplate($itemsCount, $pageItems);
    $title = 'Результаты поиска по запросу: ' . $search;

} else if ($categoryId > 0) {
    foreach ($categories as $category) {
        if ((int) $category['id'] === $categoryId) {
            $categoryName = $category['title'];
            break;
        }
    }

    if (!empty($categoryName)) {
        $itemsCount = getItemsCount($db, null, $categoryId);
        $lots = findLotsbyCategory($db, $categoryId, $pageItems, $offset);
        $pagination = getPaginationTemplate($itemsCount, $pageItems);
        $title = 'Все лоты в категории: ' . $categoryName;
    }
}

$navContent = includeTemplate(
    'nav.php',
    ['categories' => $categories]
);

$allLots = includeTemplate(
    'all-lots.php',
    ['lots' => $lots]
);

$pageContent = includeTemplate(
    'search.php',
    [
        'nav' => $navContent,
        'search' => $search,
        'lots' => $lots,
        'allLots' => $allLots,
        'categoryName' => $categoryName,
        'pagination' => $pagination,
        'title' => $title
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'nav' => $navContent,
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => $title
    ]
);

print ($layoutContent);
