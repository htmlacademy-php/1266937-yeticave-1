<?php

/**
 * @var array $categories
 */

?>

<nav class="nav">
    <ul class="nav__list container">
        <?php
        foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/search.php?category=<?= htmlspecialchars($category['id']); ?>">
                    <?= htmlspecialchars($category['title']); ?>
                </a>
            </li>
        <?php
        endforeach; ?>
    </ul>
</nav>
