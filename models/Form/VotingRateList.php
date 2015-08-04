<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;
use app\controllers\BaseController;

/**
 *
 */
class VotingRateList extends \cs\base\BaseForm
{
    const TABLE = 'cms_goods_vote_type';

    /** @var integer $id идентификатор записи */
    public $id;
    public $type;
    public $cnt;
    public $price;
    public $enabled;

    public function init()
    {
        $this->type = BaseController::getParam('type');
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'type',
                'cnt',
                'price',
            ], 'required'],
            [[
                'cnt',
                'type',
            ], 'integer'],
            [[
                'price',
            ], 'double'],
        ];
    }

    public function __construct($config = [])
    {
        self::$fields = [
            [
                'cnt', 'Значение', 1, 'integer'
            ],
            [
                'type', 'Значение', 1, 'integer'
            ],
            [
                'price', 'Множитель', 1, 'double'
            ],
        ];
        parent::__construct($config);
    }

    public function insert()
    {
        return parent::insert([
            'beforeInsert' => function($fields) {
                $fields['enabled'] = 1;
                return $fields;
            }
        ]);
    }

}
