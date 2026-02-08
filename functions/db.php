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
            } elseif (is_double($value)) {
                $type = 'd';
            }

            $types .= $type;
            $stmt_data[] = $value;
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
 *
 * @param array $config_db Массив с ключами: hostname, username, password, database
 * @throws Exception При неудачной попытке подключения
 *
 * @return mysqli Ресурс соединения
 */
function connectDb(array $configDb): mysqli
{
    try {
        $link = mysqli_connect(
            $configDb['hostname'],
            $configDb['username'],
            $configDb['password'],
            $configDb['database']
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
 *
 * @param mysqli $link Ресурс соединения
 * @throws mysqli_sql_exception Если ошибка в запросе
 *
 * @return array Возвращает массив всех категорий или пустой массив при ошибке
 */
function getCategories(mysqli $link): array
{
    try {
        $sql = 'SELECT
                    id,
                    title,
                    symbol_code
                    FROM categories';

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
 *
 * @param mysqli $link Ресурс соединения
 * @throws mysqli_sql_exception Если ошибка в запросе
 *
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
                    l.expire_at AS expiry_date,
                    c.title AS category,
                    COALESCE(MAX(b.price), l.price) AS current_price,
                    COUNT(b.id) AS bids_count
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                LEFT JOIN bids b ON l.id = b.lot_id
                WHERE l.expire_at > NOW()
                GROUP BY l.id
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
 *
 * @param mysqli $link Ресурс соединения
 * @param int $id id лота
 * @throws mysqli_sql_exception Если ошибка в запросе
 *
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
                    l.expire_at AS expiry_date,
                    l.step,
                    l.creator_id,
                    c.title AS category,
                    COALESCE(MAX(b.price), l.price) AS max_price
                FROM lots l
                JOIN categories c ON l.category_id = c.id
                LEFT JOIN bids b ON l.id = b.lot_id
                WHERE l.id = ?
                GROUP BY
                    l.id,
                    c.title';

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
 * Добавляет новый лот в базу данных
 *
 * @param mysqli $link Ресурс соединения c базой данных
 * @param array $data Массив данных из формы
 * @param int $userId Id пользователя
 * @throws Exception При ошибке запроса к базе данных
 *
 * @return int id добавленного лота
 */
function addLot(mysqli $link, array $data): int
{
    $sql = 'INSERT INTO lots(
                created_at,
                title,
                description,
                img_url,
                price,
                step,
                expire_at,
                creator_id,
                category_id
            )
            VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?)';

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
 *
 * @param mysqli $link Ресурс соединения
 * @param array $data Массив данных
 *
 * @return bool При успехе true, иначе false
 */
function addNewUser(mysqli $link, array $data): bool
{
    $sql = 'INSERT INTO users (
                created_at,
                email,
                username,
                password_hash,
                contacts
            )
            VALUES (NOW(), ?, ?, ?, ?)';

    $stmt = dbGetPrepareStmt($link, $sql, $data);

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
 *
 * @param mysqli $link Ресурс соединения
 * @param string $email Email пользователя
 * @param string $password Пароль пользователя
 * @throws Exception Если произошла ошибка
 *
 * @return array|null Данные пользователя в случае успеха, или null при ошибке
 */
function authenticateUser(mysqli $link, string $email, string $password): array|null
{
    try {
        $sql = 'SELECT
                    id,
                    username,
                    password_hash
                    FROM users
                    WHERE email = ?';

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

    } catch (mysqli_sql_exception $e) {
        error_log("Ошибка аутентификации: " . $e->getMessage());
    }

    return null;
}

/**
 * Получает количество лотов, соответствующих поисковому запросу / категории
 *
 * @param mysqli $link Ресурс соединения
 * @param string|null $search Строка поискового запроса, если есть
 * @param int|null $categoryId Id категории, если есть
 * @throws Exception Если произошла ошибка
 *
 * @return int Количество найденных лотов
 */
function getItemsCount(mysqli $link, ?string $search = null, ?int $categoryId = null): int
{
    $where = 'WHERE expire_at > NOW()';
    $params = [];

    if ($search) {
        $where .= ' AND MATCH(title, description) AGAINST(?)';
        $params[] = $search;
    }

    if ($categoryId) {
        $where .= ' AND category_id = ?';
        $params[] = $categoryId;
    }

    $sql = "SELECT COUNT(*) as cnt FROM lots $where";

    try {
        $stmt = dbGetPrepareStmt($link, $sql, $params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            return 0;
        }
        $itemsCount = mysqli_fetch_assoc($result);

        return (int) ($itemsCount['cnt'] ?? 0);

    } catch (mysqli_sql_exception $e) {
        error_log("Ошибка при получении количества лотов: " . $e->getMessage());
        return 0;
    }
}


/**
 * Выполняет полнотекстовый поиск по открытым лотам c пагинацией
 *
 * @param mysqli $link Ресурс соединения
 * @param string $search Строка поискового запроса
 * @param int $pageItems Количество лотов на страницу
 * @param int $offset Смещение
 * @throws Exception Если произошла ошибка
 *
 * @return array Список найденных лотов или пустой массив
 */
function findLotsBySearch(mysqli $link, string $search, int $pageItems, int $offset): array
{
    $sql = 'SELECT
                l.id,
                l.title,
                l.price,
                l.img_url AS url,
                l.expire_at AS expiry_date,
                c.title AS category,
                COALESCE(MAX(b.price), l.price) AS current_price,
                COUNT(b.id) AS bids_count
            FROM lots l
            JOIN categories c ON l.category_id = c.id
            LEFT JOIN bids b ON l.id = b.lot_id
            WHERE MATCH(l.title, l.description) AGAINST(?)
                AND l.expire_at > NOW()
            GROUP BY l.id
            ORDER BY l.created_at DESC
            LIMIT ? OFFSET ?';

    try {
        $stmt = dbGetPrepareStmt($link, $sql, [$search, $pageItems, $offset]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка поиска: ' . $e->getMessage());
        return [];
    }
}

/**
 * Получает список активных лотов для категории с пагинацией
 *
 * @param mysqli $link Ресурс соединения
 * @param int $category_id Id категории
 * @param int $limit Количество лотов на страницу
 * @param int $offset Смещение
 *
 * @return array Список с данными лотов или пустой массив
 */
function findLotsByCategory(mysqli $link, int $category_id, int $limit, int $offset): array
{
    $sql = 'SELECT
                l.id,
                l.title,
                l.price,
                l.img_url AS url,
                l.expire_at AS expiry_date,
                c.title AS category,
                COALESCE(MAX(b.price), l.price) AS current_price,
                COUNT(b.id) AS bids_count
            FROM lots l
            JOIN categories c ON l.category_id = c.id
            LEFT JOIN bids b ON l.id = b.lot_id
            WHERE category_id = ?
                AND expire_at > NOW()
            GROUP BY l.id
            ORDER BY l.created_at DESC
            LIMIT ? OFFSET ?';

    try {
        $stmt = dbGetPrepareStmt($link, $sql, [$category_id, $limit, $offset]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка получения лотов по категории: ' . $e->getMessage());
        return [];
    }
}

/**
 * Добавляет в БД новую ставку
 *
 * @param mysqli $link Ресурс соединения
 * @param int $bid Сумма ставки
 * @param int $lotId Id лота
 * @param int $userId Id пользователя
 *
 * @return bool При успехе true, иначе false
 */
function addBid(mysqli $link, int $bid, int $lotId, int $userId): bool
{
    $sql = 'INSERT INTO bids(
                created_at,
                price,
                lot_id,
                user_id
            )
            VALUES (NOW(), ?, ?, ?)';

    $stmt = dbGetPrepareStmt($link, $sql, [
        $bid,
        $lotId,
        $userId
    ]);

    try {
        $result = mysqli_stmt_execute($stmt);
        return $result;

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка БД при добавлении ставки: ' . $e->getMessage());
        return false;
    }
}

/**
 * Получает историю ставок для лота
 *
 * @param mysqli $link Ресурс соединения
 * @param int $lotId Id лота
 *
 * @return array Список ставок или пустой массив
 */
function getLotBids(mysqli $link, int $lotId): array
{
    $sql = 'SELECT
                b.id,
                b.created_at,
                b.price,
                u.username,
                u.id AS userId
            FROM bids b
            JOIN users u ON u.id = b.user_id
            WHERE b.lot_id = ?
            ORDER BY b.created_at DESC';

    try {
        $stmt = dbGetPrepareStmt($link, $sql, [$lotId]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $lotBids = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $lotBids ?: [];

    } catch (mysqli_sql_exception $e) {
        error_log("Ошибка при получении истории ставок для лота {$lotId}: " . $e->getMessage());
        return [];
    }
}

/**
 * Получает список ставок, сделанных пользователем
 *
 * @param mysqli $link Ресурс соединения
 * @param int $userId Id пользователя
 *
 * @return array Список ставок или пустой массив
 */
function getUserBids(mysqli $link, int $userId): array
{
    $sql = 'SELECT
                b.id,
                b.created_at,
                b.price,
                l.id AS lotId,
                l.title,
                l.img_url AS url,
                l.expire_at AS expiry_date,
                c.title AS category,
                u.contacts,
                CASE
                    WHEN l.winner_id = b.user_id
                        AND b.price = (
                            SELECT MAX(price)
                            FROM bids
                            WHERE lot_id = l.id
                        )
                    THEN 1
                    ELSE 0
                END AS isWinner,
                CASE
                    WHEN l.expire_at <= NOW() THEN 1
                    ELSE 0
                END AS isExpired
            FROM bids b
            JOIN lots l ON l.id = b.lot_id
            JOIN categories c ON c.id = l.category_id
            JOIN users u ON u.id = l.creator_id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC';

    try {
        $stmt = dbGetPrepareStmt($link, $sql, [$userId]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $userBids = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return $userBids ?: [];

    } catch (mysqli_sql_exception $e) {
        error_log("Ошибка при получении списка ставок, сделанных пользователем {$userId}: " . $e->getMessage());
        return [];
    }
}

/**
 * Получает список всех лотов без победителей, дата истечения которых меньше или равна текущей дате,
 * и находит для каждого такого лота последнюю (max) ставку
 *
 * @param mysqli $link Ресурс соединения с базой данных
 *
 * @return array Список лотов или пустой массив
 */
function getExpiredLots(mysqli $link): array
{
    try {
        $sql = 'SELECT
                    l.id,
                    l.title,
                    b.user_id,
                    u.email,
                    u.username AS winnerName
                FROM lots l
                LEFT JOIN bids b ON l.id = b.lot_id
                    AND b.price = (
                        SELECT MAX(price)
                        FROM bids
                        WHERE lot_id = l.id
                    )
                LEFT JOIN users u ON b.user_id = u.id
                WHERE l.winner_id IS NULL
                    AND l.expire_at <= NOW()
                ORDER BY l.expire_at DESC';


        $result = mysqli_query($link, $sql);

        return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка при получении списка завершенных лотов без победителя: ' . $e->getMessage());
        return [];
    }
}

/**
 * Записывает в базу данных победителя для лота
 *
 * @param mysqli $link Ресурс соединения с базой данных
 * @param int $lotId Id лота
 * @param int $userId Id победителя
 *
 * @return bool В случае успешного обновления - true, иначе false
 */
function setWinner(mysqli $link, int $userId, int $lotId): bool
{
    try {
        $sql = 'UPDATE lots
                SET winner_id = ?
                WHERE id = ?';

        $stmt = dbGetPrepareStmt($link, $sql, [$userId, $lotId]);
        return mysqli_stmt_execute($stmt);

    } catch (mysqli_sql_exception $e) {
        error_log('Ошибка при записи победителя в базу данных: ' . $e->getMessage());
        return false;
    }
}
