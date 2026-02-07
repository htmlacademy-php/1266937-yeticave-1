<?php

/**
 * @var string $navContent
 * @var string $search
 * @var string $pagination
 * @var array $lots
 * @var array $urlQuery
 */

?>

<main>
    <?= $nav; ?>
    <div class="container">
        <section class="lots">
            <?php if (!empty($lots)): ?>
                <h2><?= $title; ?></h2>
                <?= $allLots; ?>
            <?php else: ?>
                <h2><?= $title; ?></h2>
                <p>Ничего не найдено по вашему запросу</p>
            <?php endif; ?>
        </section>
        <?= $pagination; ?>
    </div>
</main>
