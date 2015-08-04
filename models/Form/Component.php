<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;
use Suffra\Config as SuffraConfig;
use app\models\Form\InstallObject;
use Spyc;

/**
 * Component
 */
class Component extends InstallObject
{
    const TABLE = 'cms_components';

    /** @var integer $id идентификатор записи */
    public $id;
    public $title;
    public $link;
    public $config;
    public $internal;
    public $author;
    public $published;
    public $version;
    public $version_dev;
    public $system;

    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'title',
        'link',
        'config',
        'internal',
        'author',
        'version',
        'system',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'title',
                'link',
                'config',
                'internal',
                'author',
                'version',
                'system',
            ], 'required'],
//            [[
//                'lang_id',
//                'general',
//            ], 'integer'],
//            [[
//                'title',
//                'description',
//                'img',
//                'link_rss',
//                'link_site',
//            ], 'string', 'min' => 1, 'max' => 100],
        ];
    }

    /**
     * Добавляет запись в таблицу
     * @return array список добавленных полей
     */
    public function insert()
    {
        $query = new Query();
        $command = $query->createCommand();
        $fields = [];
        foreach(self::$fields as $name) {
            $fields[$name] = $this->$name;
        }

        $command->insert(self::TABLE, $fields)->execute();
        $id = Yii::$app->db->getLastInsertID();
        $fields['id'] = $id;

        return $fields;
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
     * @return Component|null
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

    /**
     * Исполняет функцию инсталляции
     *
     * @param string $name название плагина
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public static function execInstall($name) {
        return self::_execInstall($name, self::TYPE_COMPONENT);
    }

    /**
     * Исполняет функцию обновления
     *
     * @param string $name название плагина
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public static function execUpgrade($name) {
        return self::_execUpgrade($name, self::TYPE_COMPONENT);
    }

    /**
     * Исполняет функцию  получения информации о модуле
     *
     * @param string $name название плагина
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public static function execInfo($name) {
        return self::_execInfo($name, self::TYPE_COMPONENT);
    }


    /**
     * Инсталлирует плагин и выполняет sql комманды которые возвращает функция компонента
     * @return array
     * [
     *     'status' - boolean результат выполнения операции
     *     'data'   - mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public function installComponent()
    {
        $result = self::execInstall($this->link);
        if ($result['status']) {
            $result = $result['data'];
            if (is_array($result)) {
                if (isset($result['sql'])) {
                    $query = new Query();
                    $command = $query->createCommand();
                    foreach($result['sql'] as $sql) {
                        $command->setSql($sql)->execute();
                    }
                }
            }
            // удаляю конфигурационный кеш
            /** @var \yii\caching\MemCache $cache */
            $cache = Yii::$app->cache;
            $cache->getMemcache()->delete('\cmsCore::getAll/items');

            return ['status' => true];
        } else {
            return $result;
        }
    }


    /**
     * Устанавливает плагин
     * и выполняет install_plugin_*()
     * @return array
     * [
     *      'status'    boolean результат выполнения операции
     *      'data'      Component или строка ошибки если status = false
     * ]
     */
    public static function install($name)
    {
        $ret = self::execInfo($name);
        if (!$ret['status']) return $ret;
        $data = $ret['data'];
        $data['published'] = 1;
        $fields = $data;
        if (isset($fields['description'])) {
            $fields['title'] = $fields['description'];
            unset($fields['description']);
        }
        if (ArrayHelper::keyExists('config', $fields)) {
            $fields['config'] = Spyc::YAMLDump($fields['config']);
        }
        (new Query())->createCommand()->insert(self::TABLE, $fields)->execute();
        $fields['id'] = \Yii::$app->db->getLastInsertID();
        $component = new self($fields);
        $ret = $component->installComponent();
        if (!$ret['status']) return $ret;

        return [
            'status' => true,
            'data'   => $component,
        ];
    }


    /**
     * Обновляет модуль
     * и версию (version) модуля в базе
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' string новая версия или строка ошибки если status = false
     * ]
     */
    public function upgrade() {
        $result = self::execUpgrade($this->link);
        if ($result['status']) {
            $result = $result['data'];
            if (is_array($result)) {
                // получаю массив для обновления
                $update = $this->getUpdateArray($result);
                foreach($update as $item) {
                    if (isset($item['sql'])) {
                        if (is_array($item['sql'])) {
                            self::executeSqlArray($item['sql']);
                        }
                    }
                    if (isset($item['config'])) {
                        if (is_array($item['config'])) {
                            $this->executeConfigArray($item['config']);
                        }
                    }
                }
            }

            (new Query())->createCommand()->update(self::TABLE, ['version' => $this->version_dev], ['id' => $this->id])->execute();
            return [
                'status' => true,
                'data' => [
                    'version' => $this->version_dev,
                ],
            ];
        } else {
            return $result;
        }
    }



    /**
     * Выдает текущие версии всех модулей
     *
     * @param array $array [
     *                          [
     * 'id' => integer
     * 'name' =>  'componentName'
     *              ], ...]
     *
     * @return array
     * [
     * 'id' => '1.2', ...
     * ]
     */
    public static function getCurrentVersions($array) {
        $ret = [];

        foreach ($array as $item) {
            $id = $item['id'];
            $name = $item['name'];
            $result = self::execInfo($name);
            if ($result['status']) {
                if ($result['data']['version']) {
                    $ret[$id] = $result['data']['version'];
                }
            }
        }

        return $ret;
    }

    /**
     * Выдает массив компонентов с полем link
     * @return array [
     * 'id' => 'link'
     * ]
     */
    public static function getLinkList() {
        return \yii\helpers\ArrayHelper::map(
            (new Query())->select('id, link')->from(self::TABLE)->orderBy('id')->all(),
            'link', 'link'
        ) ;
    }
}
