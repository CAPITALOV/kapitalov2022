<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use \Spyc;
use Suffra\Config as SuffraConfig;
use app\models\Form\InstallObject;


/**
 * Module
 */
class ModuleBind extends InstallObject
{
    const TABLE = 'cms_modules_bind';

    /** @var integer $id идентификатор записи */
    public $id;
    public $position;
    public $module_id;
    public $menu_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'menu_id',
                'position',
            ], 'required'],
            [[
                 'menu_id',
             ], 'integer'],
            [[
                 'position',
             ], 'string'],
        ];
    }

    /**
     * Добавляет запись в таблицу
     *
     * @param int $id site.cms_modules_bind.module_id
     *
     * @return integer идентификатор добавленной записи
     */
    public function insert($id)
    {
        if (!$this->validate()) return false;

        (new Query())->createCommand()->insert(self::TABLE, [
            'module_id' => $id,
            'menu_id'   => $this->menu_id,
            'position'  => $this->position,
        ])->execute();

        return true;
    }

    public static function delete($id)
    {
        (new Query())->createCommand()->delete(self::TABLE, ['id' => $id])->execute();
    }

    /**
     * @param array $fields поля для обновления
     * @param int   $id     идентификатор таблицы  site.cms_modules_bind.id
     */
    public static function update($fields, $id)
    {
        (new Query())->createCommand()->update(self::TABLE, $fields, ['id' => $id])->execute();
    }

    /**
     * Ищет запись в таблице
     * @return static
     */
    public static function find($id) {
        $query = new Query();
        $row = $query->select('*')->from(self::TABLE)->where(['module_id' => $id])->one();
        if ($row) {
            return new self($row);
        } else {
            return null;
        }
    }

    public static function getMenuList() {
        return  ArrayHelper::map((new Query())->select('id,title')->from('cms_menu')->all(), 'id', 'title');
    }
}
