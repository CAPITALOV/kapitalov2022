<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class UserSuffra
{
    const TABLE = 'cms_users';

    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    /**
     * Ищет пользователя по идентификатору
     *
     * @param integer $id идентификатор пользователя
     *
     * @return UserSuffra
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->fields['id'];
    }

    /**
     * Блокирует пользователя
     */
    public function block()
    {
        $query = new Query();
        $command = $query->createCommand();
        // ответы
        $command->update(self::TABLE, ['is_locked' => 1], ['id' => $this->getId()])->execute();

        return true;
    }

    /**
     * Блокирует пользователя
     */
    public function unblock()
    {
        $query = new Query();
        $command = $query->createCommand();
        // ответы
        $command->update(self::TABLE, ['is_locked' => 0], ['id' => $this->getId()])->execute();

        return true;
    }
    
    public function __get($name) {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->fields);
    }

}
