<?php

return [
    'facebook'     => [
        'class'        => 'app\service\authclient\Facebook',
        'clientId'     => '1114239771926233',
        'clientSecret' => '51ae4b3deed4a2c8d35356069aff55ad',
    ],
    'vkontakte'    => [
        'class'        => 'app\service\authclient\VKontakte',
        'clientId'     => '4896568',
        'clientSecret' => 'lQD6kk2VshQlxEaw27Pw',
    ],
    'google'       => [
        'class'        => 'yii\authclient\clients\GoogleOAuth',
        'clientId'     => '1010330569803-90s01v0njq8vte2eortphm7mcsvni9af.apps.googleusercontent.com',
        'clientSecret' => '5JUoqsqcMTBK7Wmvm8pbEL0w',
    ],
    'yandex_money' => [
        'class'        => 'app\service\authclient\YandexMoney',
        'clientId'     => 'B64E976FD0393C52F06BE7F6DA80983010F506D8B22B037E17458E154826D85B', // gsss
        //'clientId'     => '86CE8046687CBDBFB5CAFB93BD7B0648C70CC46EEDFEC7253A2F03CE20DE2029', // cap
        'clientSecret' => '1E420CA56AA483596A372DF5E65A8B96207692B24C08C2A3A6411566FBBCD13A5C53836A7B2459F305D60FF3CADDADB46D0371C4421B29C7915FB13D2BC93F4E',
        'scope'        => 'account-info',
    ],
];