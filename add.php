<?php

/**
 * @var int $isAuth
 * @var string $userName
 * @var mysqli $db
 */

require_once __DIR__ . '/init.php';

$categories = getCategories($db);

$errors = [];
$postData = $_POST;
$fileData = $_FILES;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = validateLotForm($postData, $fileData);

    if (empty($errors)) {
        $tmpName = $fileData['lot-img']['tmp_name'];
        $extension = pathinfo($fileData['lot-img']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $filePath = "uploads/$fileName";

        if (move_uploaded_file($tmpName, __DIR__ . '/' . $filePath)) {
            $postData['lot-img'] = $filePath;
            // Временный id
            $userId = 1;

            try {
                $id = addLot($db, $postData, $userId);
                header("Location: /lot.php?id=$id");
                exit();

            } catch (Exception $e) {
                $errors['db'] = 'Ошибка базы данных: ' . $e->getMessage();
            }
        } else {
            $errors['lot-img'] = 'Не удалось сохранить файл на сервере.';
        }
    }
}


$pageContent = includeTemplate(
    'add.php',
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
        'isAuth' => $isAuth,
        'userName' => $userName,
        'title' => 'YetiCave - Добавление лота'
    ]
);

print ($layoutContent);
