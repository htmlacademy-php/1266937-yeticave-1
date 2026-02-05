<?php

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 *
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 *
 * @return string Итоговый HTML
 */
function includeTemplate(string $name, array $data = []): string
{
    $name = 'templates/' . $name;
    $result = 'Ошибка загрузки шаблона';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    return ob_get_clean();
}

/**
 * Показывает страницу ошибки
 *
 * @param int $code Код ошибки
 * @param string $message Сообщение с описанием ошибки
 * @param array $user Данные пользователя
 * @param array $categories Список категорий
 *
 * @return void
 */
function showErrorPage(int $code, string $message, ?array $user, array $categories): void
{
    $navContent = includeTemplate(
        'nav.php',
        [
            'categories' => $categories
        ]
    );

    $mainContent = includeTemplate(
        'error.php',
        [
            'nav' => $navContent,
            'message' => $message,
            'code' => $code,
        ]
    );

    $layoutContent = includeTemplate(
        'layout.php',
        [
            'code' => $code,
            'content' => $mainContent,
            'user' => $user,
            'categories' => $categories,
        ]
    );

    http_response_code($code);

    print ($layoutContent);

    exit();
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     getNounPluralForm(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественного числа
 */
function getNounPluralForm(int $number, string $one, string $two, string $many): string
{
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Форматирует цену в рублях с разделителями разрядов, опционально добавляет знак рубля
 *
 * @param int $price Целое число
 * @param bool $withSymbol Со знаком рубля true, иначе false
 *
 * @return string Отформатированная цена
 */
function formatPrice(int $price, bool $withSymbol = true): string
{
    $formatted = number_format($price, 0, '.', ' ');

    return $withSymbol ? $formatted . '<b class="rub"></b>' : $formatted;
}

/**
 *  Возвращает количество целых часов и остатка минут до даты в будущем
 *
 * @param string $date Дата в формате ГГГГ-ММ-ДД
 *
 * @return int[] Массив: первый элемент - целое количество часов до даты, второй - остаток в минутах
 */
function getRemainingTime(string $date): array
{
    $expiryDate = date_create($date);

    if ($expiryDate === false) {
        error_log('В функцию getRemainingTime передана некорректная дата' . $date);
        return [0, 0];
    }

    $currentDate = date_create();

    if ($currentDate > $expiryDate) {
        return [0, 0];
    }

    $interval = date_diff($currentDate, $expiryDate);
    $hours = ($interval->days * 24) + $interval->h;
    $minutes = $interval->i;

    return [$hours, $minutes];
}

/**
 * Форматирует время, оставшееся до даты в будущем
 *
 * @param int[] $timeData Массив, в котором первый элемент - часы, второй - минуты
 *
 * @return string Время в формате ЧЧ:ММ (с ведущими нулями)
 */
function formatRemainingTime(array $timeData): string
{
    [$hours, $minutes] = $timeData;

    return sprintf("%02d:%02d", $hours, $minutes);
}

// function getTimePassed()
