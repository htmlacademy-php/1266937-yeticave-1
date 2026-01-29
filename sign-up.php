<?php

/**
 * @var mysqli $db
 * @var array $user
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

if ($user) {
    if (!$user) {
        showErrorPage(403, 'Доступ запрещен. Страница доступна только неавторизованным пользователям', $user, $categories);
        exit();
    }
}

$errors = [];
$postData = $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateSignUpForm($postData);
    $email = $postData['email'] ?? '';

    if (empty($errors['email'])) {
        $uniqueError = validateEmailUnique($db, $email);
        if ($uniqueError) {
            $errors['email'] = $uniqueError;
        }
    }

    if (empty($errors)) {
        $result = addNewUser($db, $postData);

        if ($result) {
            header("Location: /login.php");
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
    'sign-up.php',
    [
        'nav' => $navContent,
        'errors' => $errors,
        'postData' => $postData
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'nav' => $navContent,
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - Регистрация'
    ]
);

print ($layoutContent);
