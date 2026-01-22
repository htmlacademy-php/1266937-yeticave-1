<?php

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * isDateValid('2019-01-01'); // true
 * isDateValid('2016-02-29'); // true
 * isDateValid('2019-04-31'); // false
 * isDateValid('10.10.2010'); // false
 * isDateValid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function isDateValid(string $date): bool
{
    $formatToCheck = 'Y-m-d';
    $dateTimeObj = date_create_from_format($formatToCheck, $date);
    $errors = date_get_last_errors();

    return $dateTimeObj !== false && (!$errors || ($errors['error_count'] === 0 && $errors['warning_count'] === 0));
}

/**
 * Проверяет, не превышает ли длина строки max значение
 * @param string $value Значение для проверки
 * @param int $min Минимально возможное количество символов
 * @param int $max Максимально возможное количество символов
 * @return string|null Текст ошибки или null
 */
function validateLength(string $value, int $min, int $max): string|null
{
    if ($value) {
        $length = mb_strlen($value);
        if ($length < $min || $length > $max) {
            return "Значение должно быть от $min до $max символов";
        }
    }

    return null;
}

/**
 * Проверяет, является ли число целым и больше нуля
 * @param string $value Значение для проверки
 * @return string|null Текст ошибки или null
 */
function validatePositiveInteger(string $value): string|null
{
    $result = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if ($result === false) {
        return "Введите целое число больше 0";
    }

    return null;
}

/**
 * Проверяет дату на соответствие формату «ГГГГ-ММ-ДД», дата должна быть больше текущей хотя бы на один день
 * @param string $value Значение для проверки
 * @return string|null Текст ошибки или null
 */
function validateDate(string $value): string|null
{
    if (isDateValid($value) === false) {
        return 'Введите дату в формате «ГГГГ-ММ-ДД»';
    }

    $today = date_create('today');
    $date = date_create($value);

    date_time_set($date, 0, 0, 0);

    if ($date <= $today) {
        return 'Дата должна быть больше текущей хотя бы на один день';
    }

    return null;
}

/**
 * Проверяет MIME-тип загруженного файла
 * @param array $file Элемент массива $_FILES для загруженного файла
 * @return string|null Текст ошибки или null
 */
function validateImage(array $file): string|null
{
    $mimeType = mime_content_type($file['tmp_name']);
    $fileTypes = ['image/jpeg', 'image/png'];

    if (!in_array($mimeType, $fileTypes)) {
        return 'Допустимые форматы изображения: jpeg, png';
    }

    return null;

}

/**
 * Валидирует данные формы добавления лота
 * @param array $postData Данные из массива $_POST
 * @param array $fileData Данные из массива $_FILES
 * @return array Список ошибок
 */
function validateLotForm(array $postData, array $fileData): array
{
    $requiredFields = [
        'lot-name',
        'message',
        'lot-img',
        'category',
        'lot-rate',
        'lot-date',
        'lot-step'
    ];

    // Сообщения для обязательных полей
    $errorMessages = [
        'lot-name' => 'Введите наименование лота',
        'message' => 'Напишите описание лота',
        'lot-img' => 'Загрузите изображение',
        'category' => 'Выберите категорию',
        'lot-rate' => 'Введите начальную цену',
        'lot-date' => 'Введите дату завершения торгов',
        'lot-step' => 'Введите шаг ставки'
    ];

    $errors = [];

    $rules = [
        'lot-name' => function ($value) {
            return validateLength($value, 1, 80);
        },
        'message' => function ($value) {
            return validateLength($value, 1, 1000);
        },
        'lot-rate' => function ($value) {
            return validatePositiveInteger($value);
        },
        'lot-step' => function ($value) {
            return validatePositiveInteger($value);
        },
        'lot-date' => function ($value) {
            return validateDate($value);
        },
        'lot-img' => function ($file) {
            return validateImage($file);
        }
    ];

    // Если поле не заполнено
    foreach ($requiredFields as $key) {
        if (empty($postData[$key]) && $key !== 'lot-img') {
            $errors[$key] = $errorMessages[$key];
        }
    }

    // Если файл не загружен
    if (empty($fileData['lot-img']['name'])) {
        $errors['lot-img'] = $errorMessages['lot-img'];
    }

    foreach ($rules as $key => $rule) {
        if (empty($errors[$key])) {
            if (isset($postData[$key])) {
                $ruleError = $rule($postData[$key]);
                if ($ruleError) {
                    $errors[$key] = $ruleError;
                }
            } else if (isset($fileData[$key])) {
                $ruleError = $rule($fileData[$key]);
                if ($ruleError) {
                    $errors[$key] = $ruleError;
                }
            }
        }
    }

    return array_filter($errors);
}
