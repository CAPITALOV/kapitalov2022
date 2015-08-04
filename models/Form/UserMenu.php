<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * UserMenu
 */
class UserMenu extends Model
{
    const TABLE = 'cms_user_menu';

    /** @var integer $id идентификатор записи */
    public $id;
    /** @var integer $ord сортировка */
    public $ord;
    /** @var integer $category категория */
    public $category;
    public $url;
    public $a_id;
    /** @var string $title идентификатор строковой переменной для перевода */
    public $title;
    /** @var string $img картинка, путь=? */
    public $img;
    public $isModer;
    public $isAdmin;
    public $isMy;
    public $isFriend;
    public $isFriendNoAdd;
    public $disable;

    /** @var array $fields все названия полей таблицы */
    private static $fields = ['ord', 'category', 'url', 'a_id', 'title', 'img', 'isModer', 'isAdmin',
        'isMy', 'isFriend', 'isFriendNoAdd', 'disable'];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['category', 'url', 'title', 'img', 'isModer', 'isAdmin',
                'isMy', 'isFriend', 'isFriendNoAdd', 'disable'], 'required'],
            [['category', 'isModer', 'isAdmin',
                'isMy', 'isFriend', 'isFriendNoAdd', 'disable'], 'integer'],
            [['a_id'], 'string', 'min' => 2, 'max' => 100],
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
