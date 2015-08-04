<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class JsLoggerItem
{
    const TABLE = 'log_javascript';

    public $id;
    public $fields;

    public function __construct($fields) {
        $this->id = $fields['id'];
        $this->fields = $fields;
    }

    /**
     * Ищет строку по идентификатору
     * @param integer $id  идентификатор строки
     * @return JsLoggerItem
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
     * Удаляет строку
     */
    public function delete() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->delete(self::TABLE, ['id' => $this->getId() ])->execute();
    }

    /**
     * Удаляет все записи
     */
    public static function deleteAll() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->delete(self::TABLE)->execute();
    }
}
