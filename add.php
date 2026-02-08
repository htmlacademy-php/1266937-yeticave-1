<?php

/**
 * @var array $user
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

if (!$user) {
    showErrorPage(403, 'Доступ запрещен. Страница доступна только авторизованным пользователям', $user, $categories);
    exit();
}

$errors = [];
$postData = $_POST;
$fileData = $_FILES;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateAddLotForm($postData, $fileData);

    if (empty($errors)) {
        $tmpName = $fileData['lot-img']['tmp_name'];
        $extension = pathinfo($fileData['lot-img']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $filePath = "uploads/$fileName";

        if (move_uploaded_file($tmpName, __DIR__ . '/' . $filePath)) {
            $postData['lot-img'] = $filePath;
            $userId = $user['id'];
            $lotData = [
                $postData['lot-name'],
                $postData['message'],
                $postData['lot-img'],
                $postData['lot-rate'],
                $postData['lot-step'],
                $postData['lot-date'],
                $userId,
                $postData['category']
            ];

            try {
                $id = addLot($db, $lotData);
                header("Location: /lot.php?id={$id}");
                exit();
            } catch (Exception $e) {
                $errors['db'] = 'Ошибка базы данных: ' . $e->getMessage();
            }
        } else {
            $errors['lot-img'] = 'Не удалось сохранить файл на сервере.';
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
    'add.php',
    [
        'nav' => $navContent,
        'categories' => $categories,
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
        'title' => 'YetiCave - Добавление лота'
    ]
);

print ($layoutContent);
