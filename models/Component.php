<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class Component extends \yii\base\Object
{
    const TABLE = 'cms_components';

    public $id;
    public $title;
    public $link;
    public $config;
    public $internal;
    public $author;
    public $published;
    public $system;

    /**
     * Ищет компонент по идентификатору
     * @param integer $id  идентификатор компонента
     * @return Component
     */
    public static function find($id)
    {
        $query = new Query();
        $row = $query->select('*')->from(self::TABLE)->where(['id' => $id])->one();
        if (!$row) {
            return null;
        } else {

            return new static([
                'id'        => $row['id'],
                'title'     => $row['title'],
                'link'      => $row['link'],
                'config'    => $row['config'],
                'internal'  => $row['internal'],
                'author'    => $row['author'],
                'published' => $row['published'],
                'system'    => $row['system'],
            ]);
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
     * Публикует компонент
     */
    public function publicate() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->update(self::TABLE, [
            'published' => 1
        ], ['id' => $this->getId() ])->execute();
    }

    /**
     * Снимает с публикации компонент
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
    public function setConfig($data)
    {
        $query = new Query();
        $command = $query->createCommand();
        if ($command->update(self::TABLE, ['config' => $data], ['id' => $this->getId()])->execute()) {
            $this->config = $data;
            /** @var \yii\caching\MemCache $cache */
            $cache = \Yii::$app->cache;
            $cache->getMemcache()->delete('\cmsCore::getAll/items');

            return true;
        } else {
            return false;
        }

    }

}
