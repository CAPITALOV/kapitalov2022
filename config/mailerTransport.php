<?php

return [
    'class'            => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class'    => 'Swift_SmtpTransport',

        'username' => 'kapitalov2020',
        'password' => 'mtS-WVL-Qzr-3fW',
        'host'     => 'smtp.yandex.ru',
        'port'     => '465',
        'encryption' => 'ssl',
    ]
];