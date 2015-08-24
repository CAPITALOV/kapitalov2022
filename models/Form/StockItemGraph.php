<?php

namespace app\models\Form;

use Yii;

/**
 */
class StockItemGraph extends \cs\base\BaseForm
{
    const TABLE = 'cap_stock';

    public $dateMin;
    public $dateMax;

    function __construct($fields = [])
    {
        static::$fields = [
            [
                'dateMin',
                'От',
                1,
                'cs\Widget\DatePicker\Validator',
                'widget' => ['cs\Widget\DatePicker\DatePicker',
                    [
                        'dateFormat' => 'php:d.m.Y',
                    ]
                ]
            ],
            [
                'dateMax',
                'До',
                0,
                'cs\Widget\DatePicker\Validator',
                'widget' => ['cs\Widget\DatePicker\DatePicker',
                    [
                        'dateFormat' => 'php:d.m.Y',
                    ]
                ]
            ],
        ];
        parent::__construct($fields);
    }
}
