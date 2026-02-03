<?php

/**
 * @var array $lot
 * @var array $categories
 * @var string $navContent
 * @var int $minBid
 */

?>

<main>
    <?= $nav; ?>
    <section class="lot-item container">
        <h2><?= htmlspecialchars($lot['title']); ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src=<?= htmlspecialchars($lot['url']); ?> width="730" height="548"
                        alt=<?= htmlspecialchars($lot['title']); ?>>
                </div>
                <p class="lot-item__category">Категория:
                    <span>
                        <?= htmlspecialchars($lot['category']); ?>
                    </span>
                </p>
                <p class="lot-item__description"><?= htmlspecialchars($lot['description']); ?></p>
            </div>
            <div class="lot-item__right">
                <?php if (!empty($user)): ?>
                    <div class="lot-item__state">
                        <?php
                        $timeToExpiry = getTimeToExpiry($lot['expiry_date']);
                        $hours = $timeToExpiry[0];
                        $minutes = $timeToExpiry[1];
                        ?>
                        <div class="lot-item__timer timer <?= $hours === 0 ? 'timer--finishing' : ''; ?>">
                            <?= sprintf("%02d:%02d", $hours, $minutes) ?>
                        </div>
                        <div class="lot-item__cost-state">
                            <div class="lot-item__rate">
                                <span class="lot-item__amount">Текущая цена</span>
                                <span class="lot-item__cost"><?= htmlspecialchars($lot['max_price']); ?></span>
                            </div>
                            <div class="lot-item__min-cost">
                                Мин. ставка <span><?= htmlspecialchars($minBid) ?> р</span>
                            </div>
                        </div>
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
                    </div>
                <?php endif; ?>
                <div class="history">
                    <h3>История ставок (<span><?= count($lotBids); ?></span>)</h3>
                    <table class="history__list">
                        <?php
                        foreach ($lotBids as $lotBid): ?>
                            <tr class="history__item">
                                <td class="history__name"><?= htmlspecialchars($lotBid['userName']); ?></td>
                                <td class="history__price"><?= htmlspecialchars($lotBid['price']); ?> р</td>
                                <td class="history__time">5 минут назад</td>
                            </tr>
                            <?php
                        endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </section>
</main>
