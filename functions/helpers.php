<?php

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
 * Форматирует цену в рублях с разделителями разрядов
 * @param int $price Целое число
 * @return string Отформатированная цена со знаком рубля
 */

function formatPrice(int $price): string
{
    return number_format($price, 0, '', ' ') . '<b class="rub"></b>';
}

/**
 *  Возвращает количество целых часов и остатка минут до даты в будущем
 *
 * @param string $date Дата в формате ГГГГ-ММ-ДД
 * @return array Массив: первый элемент - целое количество часов до даты, второй - остаток в минутах
 */
function getTimeToExpiry(string $date): array
{
    $expiryDate = date_create($date);

    if ($expiryDate === false) {
        error_log('В функцию getTimeToExpiry передана некорректная дата' . $date);
        return [0, 0];
    }

    $currentDate = date_create();

    if ($currentDate > $expiryDate) {
        return [0, 0];
    }

    $interval = date_diff($currentDate, $expiryDate);

    $days = $interval->d;
    $hours = $interval->h;
    $minutes = $interval->i;

    if ($days !== 0) {
        $hours += $days * 24;
    }

    return [$hours, $minutes];
}
