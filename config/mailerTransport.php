<?php

return [
    'class'            => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class'    => 'Swift_SmtpTransport',

        'username' => 'kapitalov@kapitalov.icu',
        'password' => 'mtS-WVL-Qzr-3fW',
        'host'     => 'mail.kapitalov.icu',
        'port'     => '465',
        'encryption' => 'ssl',
    ]
];