<?php

/**
 * @var string $navContent
 * @var string $search
 * @var array $pagination
 * @var array $lots
 * @var array $urlQuery
 */

?>

<main>
    <?= $nav; ?>
    <div class="container">
        <section class="lots">
            <?php if (!empty($lots)): ?>

                <?php if (!empty($search)): ?>
                    <h2>Результаты поиска по запросу «<span><?= htmlspecialchars($search); ?></span>»</h2>
                <?php elseif (!empty($categoryName)): ?>
                    <h2>Все лоты в категории <span>«<?= htmlspecialchars($categoryName); ?>»</span></h2>
                <?php endif; ?>

                <?= includeTemplate('all-lots.php', ['lots' => $lots]); ?>

            <?php else: ?>
                <p>Ничего не найдено по вашему запросу</p>
            <?php endif; ?>
        </section>
        <?= includeTemplate('pagination.php', $pagination); ?>
    </div>
</main>
