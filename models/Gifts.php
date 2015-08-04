<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class Gifts
{
    const TABLE = 'cms_gifts';

    public $id;
    public $fields;

    public function __construct($fields) {
        $this->id = $fields['id'];
        $this->fields = $fields;
    }
    
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
     * Удаляет элемент меню
     *
     */
    public function delete() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->delete(self::TABLE, ['id' => $this->id])->execute();
    }}

