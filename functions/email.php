<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

/**
 * Создает объект Mailer на основе конфига
 *
 * @param array $config Массив с параметрами подключения
 * @return Mailer
 */
function createMailer(array $config): Mailer
{
    $dsn = sprintf(
        'smtps://%s:%s@%s:%s',
        urlencode($config['login']),
        urlencode($config['password']),
        $config['host'],
        $config['port']
    );

    $transport = Transport::fromDsn($dsn);

    return new Mailer($transport);
}

/**
 * Отправляет email-уведомление победителю
 *
 * @param Mailer $mailer Экземпляр объекта Mailer
 * @param array $config Настройки почты из config
 * @param array $lot Данные лота и победителя
 *
 * @return void
 */
function sendEmailToWinner(Mailer $mailer, array $config, array $lot): void
{

    $messageContent = includeTemplate(
        'email.php',
        [
            'userName' => $lot['winnerName'],
            'lotId' => $lot['id'],
            'lotTitle' => $lot['title'],
            'url' => $config['url'],
        ]
    );

    $message = (new Email())
        ->to($lot['email'])
        ->from($config['from'])
        ->subject('Ваша ставка победила')
        ->html($messageContent);

    $mailer->send($message);
}
