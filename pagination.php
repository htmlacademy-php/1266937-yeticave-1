<?php

$currentPage = max(1, (int) ($_GET['page'] ?? 1));

$pagesCount = (int) ceil($itemsCount / $pageItems);
$pagesCount = max(1, $pagesCount);

if ($currentPage > $pagesCount) {
    $currentPage = $pagesCount;
}

// Расчет отступа для запроса
$offset = ($currentPage - 1) * $pageItems;

// Массив номеров страниц для шаблона
$pages = range(1, $pagesCount);

// Параметры url
$queryParams = $_GET;
unset($queryParams['page']);
// Превращаем массив в строку запроса
$urlQuery = http_build_query($queryParams);
$urlQuery = $urlQuery ? $urlQuery . '&' : '';

$prevPage = ($currentPage > 1) ? $currentPage - 1 : null;
$nextPage = ($currentPage < $pagesCount) ? $currentPage + 1 : null;

$pagination = [
    'pagesCount' => $pagesCount,
    'pages' => $pages,
    'currentPage' => $currentPage,
    'prevPage' => $prevPage,
    'nextPage' => $nextPage,
    'urlQuery' => $urlQuery
];
