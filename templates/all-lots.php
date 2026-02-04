<?php

/**
 * var array $lots
 */

?>

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
                            <?= formatPrice(htmlspecialchars($lot['price'])); ?>
                        </span>
                    </div>
                    <?php [$hours, $minutes] = getRemainingTime($lot['expiry_date']); ?>
                    <div class="lot__timer timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                        <?= formatRemainingTime([$hours, $minutes]) ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
