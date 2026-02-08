<?php

/**
 * @var string $nav
 * @var array $lot
 * @var int $minBid
 * @var array $user
 * @var array $errors
 * @var array $lotBids
 * @var bool $showLotForm
 */

?>

<main>
    <?= $nav; ?>
    <section class="lot-item container">
        <h2><?= htmlspecialchars($lot['title'] ?? ''); ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= htmlspecialchars($lot['url'] ?? ''); ?>" width=" 730" height="548"
                        alt="<?= htmlspecialchars($lot['title'] ?? ''); ?>">
                </div>
                <p class="lot-item__category">Категория:
                    <span>
                        <?= htmlspecialchars($lot['category'] ?? ''); ?>
                    </span>
                </p>
                <p class="lot-item__description"><?= htmlspecialchars($lot['description'] ?? ''); ?></p>
            </div>
            <div class="lot-item__right">
                <div class="lot-item__state">
                    <?php [$hours, $minutes] = getRemainingTime($lot['expiry_date'] ?? ''); ?>
                    <div class="lot-item__timer timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                        <?= formatRemainingTime([$hours, $minutes]) ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= formatPrice($lot['max_price'] ?? 0, false); ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= formatPrice($minBid, false) ?> р</span>
                        </div>
                    </div>
                    <?php
                    if ($showAddBidForm): ?>
                        <form class="lot-item__form" action="lot.php?id=<?= $lot['id'] ?>" method="post" autocomplete="off">
                            <p
                                class="lot-item__form-item form__item <?= isset($errors['cost']) ? 'form__item--invalid' : '' ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="cost" placeholder="12 000"
                                    value="<?= htmlspecialchars($postData['cost'] ?? ''); ?>">
                                <span class="form__error"><?= $errors['cost'] ?? ''; ?></span>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                        <?php
                    endif; ?>
                </div>
                <div class="history">
                    <h3>История ставок (<span><?= count($lotBids); ?></span>)</h3>
                    <table class="history__list">
                        <?php
                        foreach ($lotBids as $bid): ?>
                            <tr class="history__item">
                                <td class="history__name"><?= htmlspecialchars($bid['username'] ?? ''); ?></td>
                                <td class="history__price"><?= formatPrice($bid['price'] ?? '', false) ?> р</td>
                                <td class="history__time"><?= getTimePassed($bid['created_at'] ?? ''); ?></td>
                            </tr>
                            <?php
                        endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
