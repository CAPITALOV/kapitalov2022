<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

class GiftsForm extends Model
{
    const TABLE = 'cms_gifts';

    
    public $id;    
    public $title;
    public $description;
    public $imageurl;
    public $cena;
    public $general;    
    public $category;
    public $date_add;    
    public $hits;
	
    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'title',
        'description',
        'imageurl',
        'cena',
        'general',
        'category',       
        'hits',
		'date_add',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'title',                
                'cena',
                'general',
				'imageurl',
                'category',                             
            ], 'required'],            
            [[
                'title',				
            ], 'string'],
			[[
                'cena',
				'general',
                'category',
            ], 'integer'],
			[[
                'imageurl',				
            ], 'file'],
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
		$fields['date_add'] = 'NOW()';
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


