<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id'           => 'basic',
    'basePath'     => dirname(__DIR__),
    'bootstrap'    => ['log'],
    'defaultRoute' => 'site/index',
    'language'     => 'ru',
    'timeZone'     => 'Europe/Moscow',
    'aliases'       => [
        '@vendor' => __DIR__ . '/../vendor',
    ],
    'components'   => [
        'authClientCollection' => [
            'class'   => 'yii\authclient\Collection',
            'clients' => require(__DIR__ . '/authClientCollection.php'),
        ],
        'request'      => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
            'enableCsrfValidation'   => false,
            'cookieValidationKey'    => '',
        ],
        'cache' =>
            (YII_ENV_PROD) ?
                [
                    'class'   => 'yii\caching\MemCache',
                    'servers' => [
                        [
                            'host' => 'localhost',
                            'port' => 11211,
                        ],
                    ],
                ] :
                [
                    'class' => 'yii\caching\FileCache',
                ],
        'user'         => [
            'identityClass'   => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer'               => require(__DIR__ . '/mailerTransport.php'),
        'log'                  => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning',
                    ],
                    'maxLogFiles' => 1,
                ],
                [
                    'class'  => 'yii\log\DbTarget',
                    'categories' => ['cap\\*'],
                ],
                [
                    'class'      => 'yii\log\EmailTarget',
                    'levels'     => [
                        'error',
                        'warning',
                    ],
                    'categories' => ['yii\db\*'],
                    'message'    => [
                        'from'    => ['admin@capitalov.com'],
                        'to'      => ['admin@capitalov.com'],
                        'subject' => 'capitalov.com ERROR',
                    ],
                ],
            ],
        ],
        'urlManager'   => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => true,
            'suffix'              => '',
            'rules'               => require(__DIR__ . '/urlRules.php'),
        ],
        'db'           => require(__DIR__ . '/db.php'),
        'i18n'         => [
            'translations' => [
                'app*' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/translations',
                    'sourceLanguage' => 'en',
                    'fileMap'        => [
                        'app' => 'messages.php'
                    ]
                ],
            ],
        ],
        'formatter'            => [
            'dateFormat'        => 'dd.MM.yyyy',
            'timeFormat'        => 'php:H:i:s',
            'datetimeFormat'    => 'php:d.m.Y H:i',
            'decimalSeparator'  => '.',
            'thousandSeparator' => ' ',
            'currencyCode'      => 'RUB',
            'locale'            => 'ru-RU',
            'nullDisplay'       => '',
        ],
    ],
    'params'       => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
