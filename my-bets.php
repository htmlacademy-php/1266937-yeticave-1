<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

if (empty($user)) {
    showErrorPage(403, 'Доступ запрещен. Страница доступна только авторизованным пользователям', $user, $categories);
    exit();
}

$userId = $user['id'] ?? null;
$userBids = getUserBids($db, $userId);

$navContent = includeTemplate(
    'nav.php',
    [
        'categories' => $categories
    ]
);

$pageContent = includeTemplate(
    'my-bets.php',
    [
        'nav' => $navContent,
        'userBids' => $userBids,
        'userId' => $userId
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'content' => $pageContent,
        'nav' => $navContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - Мои ставки'
    ]
);

print ($layoutContent);
