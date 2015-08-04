<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * TopMenu
 */
class NewsRssItem extends Model
{
    const TABLE = 'cms_news_rss';

    /** @var integer $id идентификатор записи */
    public $id;
    public $title;
    public $description;
    public $lang_id;
    public $img;
    public $link_rss;
    public $link_site;
    public $general;

    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'title',
        'description',
        'lang_id',
        'img',
        'link_rss',
        'link_site',
        'general',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'title',
                'description',
                'img',
                'link_rss',
                'link_site',
                'general',
            ], 'required'],
            [[
                'lang_id',
                'general',
            ], 'integer'],
            [[
                'title',
                'description',
                'img',
                'link_rss',
                'link_site',
            ], 'string', 'min' => 1, 'max' => 100],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * Добавляет запись в таблицу
     * @return integer идентификатор добавленной записи
     */
    public function insert()
    {
        $query = new Query();
        $command = $query->createCommand();
        $fields = [];
        foreach(self::$fields as $name) {
            $fields[$name] = $this->$name;
        }
        $command->insert(self::TABLE, $fields)->execute();
        $id = Yii::$app->db->getLastInsertID();

        return $id;
    }

    /**
     * Обновляет запись в таблицу
     * @return boolean результат операции
     */
    public function update()
    {
        $query = new Query();
        $command = $query->createCommand();
        $fields = [];
        foreach(self::$fields as $name) {
            $fields[$name] = $this->$name;
        }
        $command->update(self::TABLE, $fields, ['id' => $this->id])->execute();

        return true;
    }

    /**
     * Конструктор
     */
    public function __construct($fields = []) {
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $this->$key = $fields[$key];
            }
        }
    }

    /**
     * Ищет запись в таблице
     * @return UserMenu|null
     */
    public static function find($id) {
        $query = new Query();
        $row = $query->select('*')->from(self::TABLE)->where(['id' => $id])->one();
        if ($row) {
            return new self($row);
        } else {
            return null;
        }
    }
}
