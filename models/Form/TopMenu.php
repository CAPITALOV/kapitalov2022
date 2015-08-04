<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * TopMenu
 */
class TopMenu extends Model
{
    const TABLE = 'cms_menu';

    /** @var integer $id идентификатор записи */
    public $id;
    public $menu;
    public $title;
    public $link;
    public $linktype;
    public $linkid;
    public $target;    
    public $ordering;
    public $published;    
    public $access_list;    
	public $html_id;


    public $component;
	public $template;
	public $iconurl;
	public $iconhoverurl;
	public $NSLeft;
	public $NSRight;
	public $NSLevel;
	public $NSDiffer;
	public $NSIgnore;
	public $parent_id;


    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'menu',
        'title',
        'link',
        'linktype',
        'linkid',
        'target',        
        'ordering',
        'published',        
        'access_list',        
		'html_id',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'menu',
                'title',
                'link',
                'linktype',
                'target',
                'ordering',
                'published',                
				'html_id',
            ], 'required'],            
            [[
                'menu',
                'title',
                'link',
                'linktype',
                'linkid',
                'target',                
                'ordering',
                'link_id',
                'published',
                'access_list',                
				'html_id',
            ], 'string', 'min' => 1, 'max' => 100],
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
        $fields['ordering'] = 0;
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
