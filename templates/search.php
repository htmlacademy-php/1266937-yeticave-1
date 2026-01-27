<?php

/**
 * var string $search
 * var array $lots
 */

?>


<main>
    <nav class="nav">
        <ul class="nav__list container">
            <li class="nav__item">
                <a href="all-lots.html">Доски и лыжи</a>
            </li>
            <li class="nav__item">
                <a href="all-lots.html">Крепления</a>
            </li>
            <li class="nav__item">
                <a href="all-lots.html">Ботинки</a>
            </li>
            <li class="nav__item">
                <a href="all-lots.html">Одежда</a>
            </li>
            <li class="nav__item">
                <a href="all-lots.html">Инструменты</a>
            </li>
            <li class="nav__item">
                <a href="all-lots.html">Разное</a>
            </li>
        </ul>
    </nav>
    <div class="container">
        <section class="lots">
            <?php if (!empty($lots)): ?>
                <h2>Результаты поиска по запросу «<span><?= htmlspecialchars($search); ?></span>»</h2>
                <ul class="lots__list">
                    <?php foreach ($lots as $lot): ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src=<?= htmlspecialchars($lot['url']); ?> width="350" height="260"
                                    alt=<?= htmlspecialchars($lot['title']); ?>>
                            </div>
                            <div class="lot__info">
                                <span class="lot__category">
                                    <?= htmlspecialchars($lot['category']); ?>
                                </span>
                                <h3 class="lot__title"><a class="text-link" href='lot.php?id=<?= $lot['id']; ?>'>
                                        <?= htmlspecialchars($lot['title']); ?>
                                    </a></h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost">
                                            <?= htmlspecialchars($lot['price']); ?><b class="rub">р</b>
                                        </span>
                                    </div>
                                    <?php
                                    $timeToExpiry = getTimeToExpiry($lot['expiry_date']);
                                    $hours = $timeToExpiry[0];
                                    $minutes = $timeToExpiry[1];
                                    ?>
                                    <div class="lot__timer timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                                        <?= sprintf("%02d:%02d", $hours, $minutes) ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Ничего не найдено по вашему запросу</p>
            <?php endif; ?>
        </section>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
            <li class="pagination-item pagination-item-active"><a>1</a></li>
            <li class="pagination-item"><a href="#">2</a></li>
            <li class="pagination-item"><a href="#">3</a></li>
            <li class="pagination-item"><a href="#">4</a></li>
            <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
        </ul>
    </div>
</main>
