<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class Plugin extends \yii\base\Object
{
    const TABLE = 'cms_plugins';

    public $id;
    public $plugin;
    public $title;
    public $description;
    public $author;
    public $version;
    public $version_dev;
    public $plugin_type;
    public $published;
    public $config;

    /**
     * Ищет плагин по идентификатору
     * @param integer $id  идентификатор плагина
     * @return Component
     */
    public static function find($id)
    {
        $query = new Query();
        $row = $query->select('*')->from(self::TABLE)->where(['id' => $id])->one();
        if (!$row) {
            return null;
        } else {

            return new static($row);
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Публикует плагин
     */
    public function publicate() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->update(self::TABLE, [
            'published' => 1
        ], ['id' => $this->getId() ])->execute();
    }

    /**
     * Снимает с публикации плагин
     */
    public function unPublicate() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->update(self::TABLE, [
            'published' => 0
        ], ['id' => $this->getId() ])->execute();
    }

    /**
     * Выдат конфиг
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * Обновляет конфиг
     * @return boolean
     * true - успешно обновлено
     * false - не удачно
     */
    public function setConfig($data) {
        $query = new Query();
        $command = $query->createCommand();
        if ($command->update(self::TABLE, ['config' => $data], ['id' => $this->getId()])->execute()) {
            $this->config = $data;
            return true;
        } else {
            return false;
        }
    }
}
