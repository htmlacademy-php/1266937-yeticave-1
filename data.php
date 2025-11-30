<?php
require_once __DIR__ . '/helpers.php';

$isAuth = rand(0, 1);
$userName = 'Angelina';

$categories = [
    [
        'title' => 'Доски и лыжи',
        'mod' => 'boards'
    ],
    [
        'title' => 'Крепления',
        'mod' => 'attachment'
    ],
    [
        'title' => 'Ботинки',
        'mod' => 'boots'
    ],
    [
        'title' => 'Одежда',
        'mod' => 'clothing'
    ],
    [
        'title' => 'Инструменты',
        'mod' => 'tools'
    ],
    [
        'title' => 'Разное',
        'mod' => 'other'
    ]
];

$lots = [
    [
        'title' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '10999',
        'url' => '/img/lot-1.jpg'
    ],
    [
        'title' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'price' => '159999',
        'url' => '/img/lot-2.jpg'
    ],
    [
        'title' => 'Крепления Union Contact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => '8000',
        'url' => '/img/lot-3.jpg'
    ],
    [
        'title' => 'Ботинки для сноуборда DC Mutiny Charcoal',
        'category' => 'Ботинки',
        'price' => '10999',
        'url' => '/img/lot-4.jpg'
    ],
    [
        'title' => 'Куртка для сноуборда DC Mutiny Charcoal',
        'category' => 'Одежда',
        'price' => '7500',
        'url' => '/img/lot-5.jpg'
    ],
    [
        'title' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => '5400',
        'url' => '/img/lot-6.jpg'
    ]
];
