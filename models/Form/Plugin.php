<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;
use \Spyc;
use app\models\Form\InstallObject;

/**
 * Plugin
 */
class Plugin extends InstallObject
{
    const TABLE = 'cms_plugins';

    /** @var integer $id идентификатор записи */
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
 

    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'plugin',
        'title',
        'description',
        'author',
        'version',
        'plugin_type',
        'config',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'plugin',
                'title',
                'description',
                'author',
                'version',
                'plugin_type',
                'config',
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
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
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
     * Устанавливает плагин
     * и выполняет install_plugin_*()
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' Plugin или строка ошибки если status = false
     * ]
     */
    public static function install($name) {
        $ret = self::execInfo($name);
        if (!$ret['status']) return $ret;
        $data = $ret['data'];
        $data['published'] = 1;
        $fields = $data;
        if (ArrayHelper::keyExists('config', $fields)) {
            $fields['config'] = Spyc::YAMLDump($fields['config']);
        }
        (new Query())->createCommand()->insert(self::TABLE, $fields)->execute();
        $data['id'] = \Yii::$app->db->getLastInsertID();
        $plugin = new self($data);
        $ret = $plugin->installPlugin();
        if (!$ret['status']) return $ret;

        return [
            'status' => true,
            'data'   => $plugin,
        ];
    }

    /**
     * Ищет запись в таблице
     * @return Plugin|null
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
        return self::_execInstall($name, self::TYPE_PLUGIN);
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
        return self::_execUpgrade($name, self::TYPE_PLUGIN);
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
        return self::_execInfo($name, self::TYPE_PLUGIN);
    }


    /**
     * Инсталлирует плагин и выполняет sql комманды которые возвращает функция компонента
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public function installPlugin()
    {
        $result = self::execInstall($this->plugin);
        if ($result['status']) {
            $item = $result['data'];
            if (isset($item['sql'])) {
                self::executeSqlArray($item['sql']);
            }
            if (isset($item['events'])) {
                if (is_array($item['events'])) {
                    $this->executeEvents($item['events']);
                }
            }

            return ['status' => true];
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
     * Обновляет модуль
     * и версию (version) модуля в базе
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' string новая версия или строка ошибки если status = false
     * ]
     */
    public function upgrade() {
        $result = self::execUpgrade($this->plugin);
        if ($result['status']) {
            $result = $result['data'];
            if (is_array($result)) {
                // получаю массив для обновления
                $update = $this->getUpdateArray($result);
                foreach($update as $item) {
                    if (isset($item['sql'])) {
                        if (is_array($item['sql'])) {
                            $errors = self::executeSqlArray($item['sql']);
                            if (count($errors) > 0) {
                                return [
                                    'status' => false,
                                    'data' => 'ошибки SQL' . print_r($errors,true),
                                ];
                            }
                        }
                    }
                    if (isset($item['config'])) {
                        if (is_array($item['config'])) {
                            $this->executeConfigArray($item['config']);
                        }
                    }
                    if (isset($item['events'])) {
                        if (is_array($item['events'])) {
                            $this->executeEvents($item['events']);
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
     * Обновляет события плагина
     * @param array $events
     */
    public function executeEvents($events) {
        $table = 'cms_event_hooks';
        $command = (new Query())->createCommand();
        if (isset($events['delete'])) {
            foreach($events['delete'] as $event) {
                $command->delete($table, [
                    'event' => $event,
                    'plugin_id' => $this->id,
                ])->execute();
            }
        }
        if (isset($events['add'])) {
            foreach($events['add'] as $event) {
                $command->insert($table, [
                    'event' => $event,
                    'plugin_id' => $this->id,
                ])->execute();
            }
        }
    }
}
