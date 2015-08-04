<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class Module extends \yii\base\Object
{
    const TABLE = 'cms_modules';

    public $id;
    /* Позиция показа по-умолчанию — должна присутствовать в шаблоне */
    public $position;
    public $name;
    public $title;
    public $is_external;
    public $content;
    public $ordering;
    public $showtitle;
    public $published;
    public $user;
    public $config;
    public $original;
    /* CSS префикс */
    public $css_prefix;
    /* Доступ */
    public $access_list;
    /* Кешировать модуль? 0 - нет, 1 - да */
    public $cache;
    /* еденица измерения времени для кеша (HOUR/MINUTE/день?/месяц?) */
    public $cachetime;
    /* количество едениц измерения времени */
    public $cacheint;
    /* Шаблон модуля — Файлы из папки modules/ вашего шаблона, названия которых начинаются на module */
    public $template;
    public $is_strict_bind;
    public $version;
    public $version_dev;

    /**
     * Ищет модуль по идентификатору
     * @param integer $id  идентификатор модуля
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
