<?php

/**
 * @var string $nav
 * @var array $userBids
 */

?>

<main>
    <?= $nav; ?>
    <section class="rates container">
        <h2>Мои ставки</h2>
        <table class="rates__list">
            <?php
            foreach ($userBids as $bid): ?>
                <?php
                $isWinner = (bool) ($bid['isWinner'] ?? false);
                $isExpired = (bool) ($bid['isExpired'] ?? false);
                $className = $isWinner ? 'rates__item--win' : ($isExpired ? 'rates__item--end' : '');
                ?>
                <tr class="rates__item <?= $className; ?>">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="<?= htmlspecialchars($bid['url'] ?? ''); ?>" width="54" height="40"
                                alt="<?= htmlspecialchars($bid['title'] ?? ''); ?>">
                        </div>
                        <div>
                            <h3 class="rates__title">
                                <a href="lot.php?id=<?= $bid['lotId'] ?? '' ?>">
                                    <?= htmlspecialchars($bid['title'] ?? ''); ?>
                                </a>
                            </h3>
                            <?php if ($isWinner): ?>
                                <p><?= htmlspecialchars($bid['contacts'] ?? ''); ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="rates__category">
                        <?= htmlspecialchars($bid['category'] ?? ''); ?>
                    </td>
                    <td class="rates__timer">
                        <?php if ($isWinner): ?>
                            <div class="timer timer--win">Ставка выиграла</div>
                        <?php elseif ($isExpired): ?>
                            <div class="timer timer--end">Торги окончены</div>
                        <?php else: ?>
                            <?php
                            [$hours, $minutes] = getRemainingTime($bid['expiry_date'] ?? '');
                            ?>
                            <div class="timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                                <?= formatRemainingTime([$hours, $minutes]) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="rates__price">
                        <?= formatPrice($bid['price'] ?? 0, false); ?> р
                    </td>
                    <td class="rates__time">
                        <?= getTimePassed($bid['created_at']); ?>
                    </td>
                </tr>
                <?php
            endforeach; ?>
        </table>
    </section>
</main>
