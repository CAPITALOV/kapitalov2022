<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 *
 */
class Communities extends \cs\base\BaseForm
{
    const TABLE = 'cms_communities_category';

    /** @var integer $id идентификатор записи */
    public $id;
    public $title;
    public $general;
    public $order;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'title',
                'general',
            ], 'required'],
            [[
                'general',
            ], 'integer'],
            [[
                'title',
            ], 'string', 'min' => 1, 'max' => 600],
        ];
    }

    public function __construct($config = [])
    {
        self::$fields = [
            [
                'title', 'Название', 1, 'string', ['min' => 1, 'max' => 600]
            ],
            [
                'general', 'Название', 1, 'integer'
            ],
        ];
        parent::__construct($config);
    }

}
