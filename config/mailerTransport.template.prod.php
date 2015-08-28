<?php


return [
    'class'            => 'yii\swiftmailer\Mailer',
    'useFileTransport' => true,
    'transport'        => [
        'class'    => 'Swift_SmtpTransport',
        'host'     => 'host',
        'port'     => 'port',
        'username' => 'login',
        'password' => 'pass',
    ]
];
