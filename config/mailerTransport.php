<?php

return [
    'class'            => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class'    => 'Swift_SmtpTransport',
//        'host'     => 'smtp.timeweb.ru',
//        'port'     => '25',
//        'username' => 'info@s-routes.com',
//        'password' => 'f6Fx66CX',

        'username' => 'kapitalov2020@ya.ru',
        'password' => 'mtS-WVL-Qzr-3fW',
        'host'     => 'smtp.yandex.ru',
        'port'     => 465,
        'encryption' => 'SSL',
    ]
];