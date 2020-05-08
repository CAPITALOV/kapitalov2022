<?php

return [
    'class'            => 'yii\swiftmailer\Mailer',
    'useFileTransport' => false,
    'transport' => [
        'class'    => 'Swift_SmtpTransport',

//        'username' => 'smtp@kasianov.com',
//        'password' => '5Rs-Xgh-AQa-jJE',
        'host'     => 'mail.kasianov.com',
        'port'     => '25',
        'username' => 'smtp@kasianov.com',
        'password' => '5Rs-Xgh-AQa-jJE',
    ]
];