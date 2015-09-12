<?php

namespace app\models\Form;

use app\models\NewsItem;
use app\models\User;
use app\services\GsssHtml;
use cs\services\Str;
use cs\services\VarDumper;
use Yii;
use yii\base\Model;
use cs\Widget\FileUpload2\FileUpload;
use yii\db\Query;
use yii\helpers\Html;

/**
 * ContactForm is the model behind the contact form.
 */
class Stock extends \cs\base\BaseForm
{
    const TABLE = 'cap_stock';

    public $id;
    public $name;
    public $logo;
    public $description;
    public $finam_em;
    public $finam_market;
    public $finam_code;

    function __construct($fields = [])
    {
        static::$fields = [
            [
                'name',
                'Название',
                1,
                'string'
            ],
            [
                'description',
                'Описание',
                1,
                'string',
                [],
                'до 255 символов'
            ],
            [
                'finam_em',
                'Идентификатор котировки по Finam',
                0,
                'integer',
            ],
            [
                'finam_code',
                'CODE по Finam',
                0,
                'string',
            ],
            [
                'finam_market',
                'Идентификатор рынка по Finam',
                0,
                'integer',
            ],
            [
                'logo',
                'Картинка',
                0,
                'string',
                'widget' => [
                    FileUpload::className(),
                    [
                        'options' => [
                            'small' => [200,200]
                        ]
                    ]
                ]
            ],
        ];
        parent::__construct($fields);
    }
}
