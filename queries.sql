USE yeticave;

INSERT INTO categories (title, symbol_code)
VALUES ('Доски и лыжи', 'boards'),
  ('Крепления', 'attachment'),
  ('Ботинки', 'boots'),
  ('Одежда', 'clothing'),
  ('Инструменты', 'tools'),
  ('Разное', 'other');

INSERT INTO users (email, username, password_hash, contacts)
VALUES (
    'petrov@yandex.ru',
    'Петя',
    '$2a$12$',
    'тел. +79057659856'
  ),
  (
    'masha0789@mail.ru',
    'Мария',
    'j5b2yrp',
    'тел. +79626453245'
  );

INSERT INTO lots (
    created_at,
    title,
    description,
    img_url,
    price,
    expiry_at,
    step,
    creator_id,
    category_id
  )
VALUES (
    '2025-11-30 22:11:00',
    '2014 Rossignol District Snowboard',
    'Легкий маневренный сноуборд',
    '/img/lot-1.jpg',
    10999,
    '2025-12-01',
    100,
    1,
    1
  ),
  (
    '2025-11-30 15:20:00',
    'DC Ply Mens 2016/2017 Snowboard',
    'Легкий маневренный сноуборд',
    '/img/lot-2.jpg',
    159999,
    '2025-12-01',
    100,
    2,
    1
  ),
  (
    '2025-12-22 14:18:00',
    'Крепления Union Contact Pro 2015 года размер L/XL',
    'Легкий маневренный сноуборд',
    '/img/lot-3.jpg',
    8000,
    '2025-12-24',
    100,
    1,
    2
  ),
  (
    '2025-11-30 11:15:00',
    'Ботинки для сноуборда DC Mutiny Charcoal',
    'Легкий маневренный сноуборд',
    '/img/lot-4.jpg',
    10999,
    '2025-12-01',
    100,
    2,
    3
  ),
  (
    '2025-12-23 10:16:00',
    'Куртка для сноуборда DC Mutiny Charcoal',
    'Легкий маневренный сноуборд',
    '/img/lot-5.jpg',
    7500,
    '2025-12-25',
    100,
    1,
    4
  ),
  (
    '2025-12-23 18:20:00',
    'Маска Oakley Canopy',
    'Легкий маневренный сноуборд',
    '/img/lot-6.jpg',
    5400,
    '2025-12-26',
    100,
    2,
    6
  );

INSERT INTO bids(created_at, price, user_id, lot_id)
VALUES ('2025-12-21 15:20:00', 8000, 1, 5),
  ('2025-12-22 18:17:00', 5400, 2, 3),
  ('2025-12-22 01:26:00', 6000, 1, 3),
  ('2025-12-23 15:30:00', 10500, 2, 3);

-- получить все категории
SELECT *
from categories;

-- получить самые новые, открытые лоты.
-- Каждый лот должен включать название, стартовую цену,
-- ссылку на изображение, цену, название категории;
SELECT l.title AS lot_title,
  l.price,
  l.img_url,
  MAX(b.price) AS max_price,
  c.title AS category_title
FROM lots l
  JOIN categories c ON l.category_id = c.id
  LEFT JOIN bids b ON l.id = b.lot_id
WHERE l.expiry_at > NOW()
GROUP BY l.id
ORDER BY max_price DESC;

-- показать лот по его ID.
-- Получите также название категории, к которой принадлежит лот;
SELECT l.*,
  c.title AS category_title
from lots l
  JOIN categories c ON l.category_id = c.id
WHERE l.id = 4;

-- обновить название лота по его идентификатору
UPDATE lots
SET title = 'Ботинки для сноуборда Northwave FREEDOM SPIN'
WHERE id = 4;

-- получить список ставок для лота по его идентификатору
-- с сортировкой по дате
SELECT b.*
from bids b
  JOIN lots l ON l.id = b.lot_id
WHERE l.id = 3
ORDER BY b.created_at DESC;
