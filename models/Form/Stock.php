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
