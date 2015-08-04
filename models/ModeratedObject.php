<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @author Andrew Lykov <andrew.lykov@yandex.ru>
 */
class ModeratedObject extends ActiveRecord {

    public static $tableName;

    /**
     *
     * @var array
     */
    protected $fields;

    public function __get($name) {
        return isset($this->fields[$name]) ? $this->fields[$name] : null;
    }

    public function __set($name, $value) {
        $this->fields[$name] = $value;
    }

    public function __isset($name) {
        return isset($this->fields);
    }

    public static function tableName() {
        if(!isset(self::$tableName))
            throw new \Exception(sprintf("Please specify tableName firsl for %s", __CLASS__));
        
        return self::$tableName;
    }

}
