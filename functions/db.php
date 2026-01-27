<?php

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function dbGetPrepareStmt(mysqli $link, string $sql, array $data = []): mysqli_stmt
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } else if (is_string($value)) {
                $type = 's';
            } else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Устанавливает новое соединение с базой данных
 * @param array $config_db Массив с ключами: hostname, username, password, database
 * @throws Exception При неудачной попытке подключения
 * @return mysqli Ресурс соединения
 */
function connectDB(array $config_db): mysqli
{
    try {
        $link = mysqli_connect(
            $config_db['hostname'],
            $config_db['username'],
            $config_db['password'],
            $config_db['database']
        );

        mysqli_set_charset($link, "utf8mb4");

        return $link;

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка при подключении к базе данных:' . $e->getMessage());
        throw new Exception('Сервис временно недоступен');
    }
}

/**
 * Получает список всех категорий из базы данных
 * @param mysqli $link Ресурс соединения
 * @throws mysqli_sql_exception Если ошибка в запросе
 * @return array Возвращает массив всех категорий или пустой массив при ошибке
 */
function getCategories(mysqli $link): array
{
    try {
        $sql = 'SELECT id, title, symbol_code FROM categories';
        $result = mysqli_query($link, $sql);
        $categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $categories;

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка запроса к базе данных getCategories:' . $e->getMessage());
        return [];
    }
}

/**
 * Получает список последних 6 активных лотов, отсортированных по убыванию
 * @param mysqli $link Ресурс соединения
 * @throws mysqli_sql_exception Если ошибка в запросе
 * @return array Возвращает массив лотов или пустой массив при ошибке
 */
function getLots(mysqli $link): array
{
    try {
        $sql = 'SELECT
                    l.id,
                    l.title,
                    l.price,
                    l.img_url AS url,
                    l.expiry_at AS expiry_date,
                    c.title AS category
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                WHERE l.expiry_at > NOW()
                ORDER BY l.created_at DESC';


        $result = mysqli_query($link, $sql);
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $lots;

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка запроса к базе данных getLots:' . $e->getMessage());
        return [];
    }
}

/**
 * Получает данные одного лота по его id
 * @param mysqli $link Ресурс соединения
 * @param int $id id лота
 * @throws mysqli_sql_exception Если ошибка в запросе
 * @return array Возвращает лот или null при ошибке
 */
function getLotById(mysqli $link, int $id): ?array
{
    try {
        $sql = 'SELECT
                    l.id,
                    l.title,
                    l.description,
                    l.img_url AS url,
                    l.price,
                    l.expiry_at AS expiry_date,
                    c.title AS category,
                    COALESCE(MAX(b.price), l.price) AS max_price
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                LEFT JOIN bids b ON l.id = b.lot_id
                WHERE l.id = ?
                GROUP BY l.id, c.title';

        $stmt = dbGetPrepareStmt($link, $sql, [$id]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $lot = mysqli_fetch_assoc($result);

        return $lot ?: null;

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка при получении лота по id' . $e->getMessage());
        return null;
    }
}

/**
 * Добавляет в базу данных новый лот
 * @param mysqli $link Ресурс соединения
 * @param array $data Массив данных
 * @param int $userId
 * @throws Exception
 * @return int
 */
function addLot(mysqli $link, array $data, int $userId): int
{
    $sql = "INSERT INTO lots(created_at, title, description, img_url, price, step, expiry_at, creator_id, category_id)
            VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";

    $data = [
        $data['lot-name'],
        $data['message'],
        $data['lot-img'],
        $data['lot-rate'],
        $data['lot-step'],
        $data['lot-date'],
        $userId,
        $data['category']
    ];

    try {
        $stmt = dbGetPrepareStmt($link, $sql, $data);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            throw new Exception(mysqli_error($link));
        }

        return mysqli_insert_id($link);

    } catch (Exception $e) {
        error_log('Ошибка БД при добавлении лота: ' . $e->getMessage());
        throw new Exception('Ошибка при добавлении лота');
    }

}

/**
 * Добавляет нового пользователя в базу данных
 * @param mysqli $link Ресурс соединения
 * @param array $data Массив данных
 * @return bool При успехе true, иначе false
 */
function addNewUser(mysqli $link, array $data): bool
{
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (created_at, email, username, password_hash, contacts)
            VALUES (NOW(), ?, ?, ?, ?)';

    $stmt = dbGetPrepareStmt($link, $sql, [
        $data['email'],
        $data['name'],
        $password,
        $data['message']
    ]);

    try {
        $result = mysqli_stmt_execute($stmt);
        return $result;

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка БД при регистрации нового пользователя' . $e->getMessage());
        return false;
    }
}

/**
 * Проверяет учетные данные пользователя - email, пароль
 * @param mysqli $link Ресурс соединения
 * @param string $email Email пользователя
 * @param string $password Пароль пользователя
 * @throws Exception Если произошла ошибка
 * @return array|null Данные пользователя в случае успеха, или null при ошибке
 */
function authenticateUser(mysqli $link, string $email, string $password): array|null
{
    try {
        $sql = 'SELECT id, username, password_hash FROM users WHERE email = ?';
        $stmt = dbGetPrepareStmt($link, $sql, [$email]);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Не удалось выполнить запрос к БД");
        }

        $result = mysqli_stmt_get_result($stmt);

        $userData = $result ? mysqli_fetch_array($result, MYSQLI_ASSOC) : null;

        if ($userData && password_verify($password, $userData['password_hash'])) {
            unset($userData['password_hash']);
            return $userData;
        }

    } catch (Exception $e) {
        error_log("Ошибка аутентификации: " . $e->getMessage());
        throw $e;
    }

    return null;
}

/**
 * Выполняет полнотекстовый поиск по открытым лотам
 * @param mysqli $link Ресурс соединения
 * @param string $search Строка поискового запроса
 * @return array Список найденных лотов
 */
function getLotsViaSearch(mysqli $link, string $search): array
{
    $sql = 'SELECT
                l.id,
                l.title,
                l.price,
                l.img_url AS url,
                l.expiry_at AS expiry_date,
                c.title AS category
            FROM lots l
            JOIN categories c ON l.category_id = c.id
            WHERE MATCH(l.title, l.description) AGAINST(?)
            AND l.expiry_at > NOW()
            ORDER BY created_at DESC
            LIMIT 9';


    try {
        $stmt = dbGetPrepareStmt($link, $sql, [$search]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

    } catch (Exception $e) {
        error_log('Ошибка поиска: ' . $e->getMessage());
        return [];
    }

    return [];
}

