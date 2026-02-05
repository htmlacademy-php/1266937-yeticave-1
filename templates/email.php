<?php

/**
 * @var string $userName
 * @var string $url
 * @var string $lotTitle
 * @var int $lotId
 */

?>

<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= htmlspecialchars($userName); ?></p>
<p>Ваша ставка для лота <a href="<?= "{$url}/lot.php?id={$lotId}"; ?>"><?= htmlspecialchars($lotTitle); ?></a> победила.
</p>
<p>Перейдите по ссылке <a href="<?= "{$url}/my-bets.php"; ?>">мои ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет-Аукцион "YetiCave"</small>
