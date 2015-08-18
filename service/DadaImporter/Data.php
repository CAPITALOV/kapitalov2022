<?php
/**
 * Данные для подключения к импорту данных
 */

namespace app\service\DadaImporter;

class Data
{
    public static $importerData = [
        [
            'class' => 'app\service\DadaImporter\Finam',
            'stock_id' => 1,
            'path'     => '/SBERP_150701_150818.txt',
            'params'   => [
                'market'    => 1,
                'em'        => 23,
                'code'      => 'SBERP',       // кодовый шифр продукта
                'cn'        => 'SBERP',       // кодовый шифр продукта
            ],
        ],
        [
            'class' => 'app\service\DadaImporter\Finam',
            'stock_id' => 4,
            'path'     => '/US2.AAPL_150818_150818.txt',
            'params'   => [
                'market'    => 25,
                'em'        => 20569,
                'code'      => 'US2.AAPL',       // кодовый шифр продукта
                'cn'        => 'US2.AAPL',       // кодовый шифр продукта
            ],
        ],
        [
            'class' => 'app\service\DadaImporter\Finam',
            'stock_id' => 5,
            'path'     => '/GAZP_150818_150818.txt',
            'params'   => [
                'market'    => 1,
                'em'        => 16842,
                'code'      => 'GAZP',       // кодовый шифр продукта
                'cn'        => 'GAZP',       // кодовый шифр продукта
            ],
        ],
    ];
} 