<?php

/**
 * @var string $title;
 * @var string $message;
 * @var string $navContent
 */

?>

<main>
    <?= $nav; ?>
    <section class="lot-item container">
        <h2>Ошибка <?= $code; ?></h2>
        <p><?= $message; ?></p>
    </section>
</main>
