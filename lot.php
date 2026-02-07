<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);
$lotId = (int) filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$lotId || $lotId <= 0) {
    showErrorPage(404, 'Некорректный идентификатор лота', $user, $categories);
}

$lot = getLotById($db, $lotId);

if ($lot === null) {
    showErrorPage(404, 'Лот не найден', $user, $categories);
}

$lotBids = getLotBids($db, $lotId);

$currentPrice = $lot['max_price'] ?? $lot['price'];
$step = $lot['step'];
$minBid = $currentPrice + $step;

$isAuth = ($user !== null);
$isAuthor = ($isAuth && $user['id'] === $lot['creator_id']);
$isExpired = strtotime($lot['expiry_date']) <= time();
$lastBidUserId = !empty($lotBids) ? (int) $lotBids[0]['userId'] : null;
$isLastBidder = ($isAuth && $user['id'] === $lastBidUserId);

$showLotForm = $isAuth && !$isExpired && !$isAuthor && !$isLastBidder;

$errors = [];
$postData = $_POST;

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
        'lotBids' => $lotBids,
        'showLotForm' => $showLotForm
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
