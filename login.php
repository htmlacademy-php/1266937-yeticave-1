<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

$errors = [];
$postData = $_POST;

$navContent = includeTemplate(
    'nav.php',
    [
        'categories' => $categories
    ]
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateLoginForm($postData);

    if (empty($errors)) {
        $userData = authenticateUser($db, $postData['email'], $postData['password']);

        if ($userData) {
            $_SESSION['user'] = $userData;
            header("Location: /index.php");
            exit();

        } else {
            $errors['password'] = 'Вы ввели неверный email/пароль';
        }
    }

    $pageContent = includeTemplate(
        'login.php',
        [
            'nav' => $navContent,
            'categories' => $categories,
            'postData' => $postData,
            'errors' => $errors
        ]
    );

} else {

    if ($user) {
        header("Location: /index.php");
        exit();
    }

    $pageContent = includeTemplate('login.php', [
        'nav' => $navContent,
        'categories' => $categories,
    ]);
}

$layoutContent = includeTemplate(
    'layout.php',
    [
        'nav' => $navContent,
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - Вход'
    ]
);

print ($layoutContent);
