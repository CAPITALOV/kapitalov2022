<?php

return [
    'facebook'     => [
        'class'        => 'app\service\authclient\Facebook',
        'clientId'     => '',
        'clientSecret' => '',
    ],
    'vkontakte'    => [
        'class'        => 'app\service\authclient\VKontakte',
        'clientId'     => '',
        'clientSecret' => '',
    ],
    'google'       => [
        'class'        => 'yii\authclient\clients\GoogleOAuth',
        'clientId'     => '',
        'clientSecret' => '',
    ],
    'yandex_money' => [
        'class'        => 'app\service\authclient\YandexMoney',
        'clientId'     => '', // gsss
        //'clientId'     => '', // cap
        'clientSecret' => '',
        'scope'        => 'account-info',
    ],
];