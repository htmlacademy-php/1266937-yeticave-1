<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lotId = (int) filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$lotId || $lotId <= 0) {
    http_response_code(404);
    exit('Некорректный идентификатор лота');
}

$lot = getLotById($db, $lotId);

if ($lot === null) {
    http_response_code(404);
    die("Лот не найден");
}

$lotBids = getBidsByLot($db, $lotId);

$errors = [];
$postData = $_POST;
$currentPrice = $lot['max_price'] ?? $lot['price'];
$step = $lot['step'];
$minBid = $currentPrice + $step;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($user)) {
    $errors = validateAddBidForm($postData, $currentPrice, $step);

    if (empty($errors)) {
        $bid = (int) $postData['cost'];
        $result = addBid($db, $bid, $lotId, $user['id']);

        if ($result) {
            header("Location: /lot.php?id={$lotId}");
            exit();
        }

    }
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
        'user' => $user,
        'minBid' => $minBid,
        'errors' => $errors,
        'postData' => $postData,
        'lotBids' => $lotBids
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
