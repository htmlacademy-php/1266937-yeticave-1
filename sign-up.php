<?php

/**
 * @var mysqli $db
 * @var array $user
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

if ($user) {
    http_response_code(403);

    // $pageContent = includeTemplate('error.php', [
    //     'error' => 'Ошибка 403. Доступ запрещен'
    // ]);
    exit();
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

$pageContent = includeTemplate(
    'sign-up.php',
    [
        'categories' => $categories,
        'errors' => $errors,
        'postData' => $postData
    ]
);

$layoutContent = includeTemplate(
    'layout.php',
    [
        'content' => $pageContent,
        'categories' => $categories,
        'user' => $user,
        'title' => 'YetiCave - Регистрация'
    ]
);

print ($layoutContent);
