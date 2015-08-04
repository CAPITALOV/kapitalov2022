<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class UserMenu
{
    const TABLE = 'cms_user_menu';

    public $id;
    public $fields;

    public function __construct($fields) {
        $this->id = $fields['id'];
        $this->fields = $fields;
    }

    /**
     * Ищет плагин по идентификатору
     * @param integer $id  идентификатор плагина
     * @return UserMenu
     */
    public static function find($id)
    {
        $query = new Query();
        $row = $query->select('*')->from(self::TABLE)->where(['id' => $id])->one();
        if (!$row) {
            return null;
        } else {

            return new self($row);
        }
    }

    /**
     * Выдает идентификатор элемента
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Обновляет сортировку
     * @param array $ids [1,2,3, ...] массив идентификаторов строк по возрастанию сортировки
     *
     */
    public static function resort($ids){
        $query = new Query();
        $command = $query->createCommand();
        $c = 1;
        foreach($ids as $id) {
            $command->update(self::TABLE, [
                'ord' => $c
            ], ['id' => $id ])->execute();
            $c++;
        }
    }

    /**
     * Удаляет элемент меню
     *
     */
    public function delete() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->delete(self::TABLE, ['id' => $this->id])->execute();
    }
}
