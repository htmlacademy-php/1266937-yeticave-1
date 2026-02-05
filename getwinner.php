<?php
/**
 * @var mysqli $db
 * @var array $config
 */

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

require_once __DIR__ . '/functions/email.php';

$expiredLots = getExpiredLots($db);

$mailer = createMailer($config['mailer']);

foreach ($expiredLots as $lot) {
    if (!empty($lot['user_id'])) {
        setWinner($db, $lot['user_id'], $lot['id']);

        try {
            sendEmailToWinner($mailer, $config['mailer'], $lot);
        } catch (TransportExceptionInterface $e) {
            error_log('Ошибка при отправке письма ' . $e->getMessage());
        }
    }
}
