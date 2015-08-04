<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use \Spyc;
use Suffra\Config as SuffraConfig;
use app\models\Form\InstallObject;


/**
 * Module
 */
class Module extends InstallObject
{
    const TABLE = 'cms_modules';

    /** @var integer $id идентификатор записи */
    public $id;
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
    public $css_prefix;
    public $access_list;
    public $cache;
    public $cachetime;
    public $cacheint;
    public $template;
    public $is_strict_bind;
    public $version;
    public $version_dev;

    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'position',
        'name',
        'title',
        'is_external',
        'content',
        'ordering',
        'showtitle',
        'user',
        'config',
        'original',
        'css_prefix',
        'access_list',
        'cache',
        'cachetime',
        'cacheint',
        'template',
        'is_strict_bind',
        'version',
    ];

    /** @var array $fields поля для добавления ['имя поля', 'значние по умолчанию, если значение равно нулю из формы']*/
    private static $fieldsAdd = [
        ['position'],
        ['name'],
        ['title'],
        ['is_external'],
        ['content'],
        ['ordering'],
        ['showtitle'],
        ['published', 1],
        ['user'],
        ['config'],
        ['original'],
        ['css_prefix', ''],
        ['access_list'],
        ['cache', 0],
        ['cachetime', 0],
        ['cacheint', ''],
        ['template'],
        ['is_strict_bind'],
        ['version'],
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'position',
                'name',
                'title',
                'is_external',
                'content',
                'ordering',
                'showtitle',
                'user',
                'original',
                'template',
                'is_strict_bind',
                'version',
            ], 'required'],
            ['access_list', 'integer'],
//            [[
//                'lang_id',
//                'general',
//            ], 'integer'],
            [[
                'css_prefix',
            ], 'string'],
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
     * @return integer идентификатор добавленной записи
     */
    public function insert()
    {
        $query = new Query();
        $command = $query->createCommand();
        $fields = [];
        foreach(self::$fieldsAdd as $field) {
            $name = $field[0];
            if ($name == 'access_list') {
                if (count($this->access_list) > 0) {
                    $this->access_list = Spyc::YAMLDump($this->access_list);
                } else {
                    $this->access_list = '';
                }
            }
            if (count($field) > 1) {
                if (is_null($this->$name)) {
                    $this->$name = $field[1];
                }
            }
            $fields[$name] = $this->$name;
        }
        $fields['ordering'] = 0;
        $fields['config'] = '';
        $command->insert(self::TABLE, $fields)->execute();
        $id = Yii::$app->db->getLastInsertID();

        return $id;
    }

    /**
     * Обновляет запись в таблицу
     * @return boolean результат операции
     */
    public function update()
    {
        $query = new Query();
        $command = $query->createCommand();
        $fields = [];
        foreach(self::$fields as $name) {
            if ($name == 'access_list') {
                if (count($this->access_list) > 0) {
                    $this->access_list = Spyc::YAMLDump($this->access_list);
                } else {
                    $this->access_list = '';
                }
            }
            $fields[ $name ] = $this->$name;
        }
        $command->update(self::TABLE, $fields, ['id' => $this->id])->execute();

        return true;
    }

    /**
     * Конструктор
     */
    public function __construct($fields = []) {
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if ($key == 'access_list') {
                    $value = Spyc::YAMLLoadString($value);
                }
                $this->$key = $value;
            }
        }
    }

    /**
     * Ищет запись в таблице
     * @return Module|null
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
     * Выдает список групп пользователей сайта
     * ['id' => 'title', ...]
     */
    public static function getSiteGroups()
    {
        $query = new Query();
        $rows = $query->select('id,title')->from('cms_user_groups')->all();
        return ArrayHelper::map($rows, 'id','title');
    }

    /**
     * Инсталлирует модуль и выполняет sql комманды которые возвращает функция компонента
     *
     * @return array
     * [
     *      'status' boolean результат выполнения операции
     *      'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public function installModule()
    {
        $name = $this->content;
        if (self::isModuleName($name)) {
            $result = self::execInstall($name);
            if ($result['status']) {
                if (isset($result['sql'])) {
                    $query = new Query();
                    $command = $query->createCommand();
                    foreach($result['sql'] as $sql) {
                        $command->setSql($sql)->execute();
                    }
                }

                return $result;
            } else {
                return $result;
            }
        } else {
            return [
                'status' => false,
                'data'   => 'Нет файла модуля',
            ];
        }
    }


    /**
     * Устанавливает плагин
     * и выполняет install_plugin_*()
     * @return array
     * [
     *      'status'    boolean результат выполнения операции
     *      'data'      Plugin или строка ошибки если status = false
     * ]
     */
    public static function install($name)
    {
        $ret = self::execInfo($name);
        if (!$ret['status']) return $ret;
        $data = $ret['data'];
        $data['published'] = 1;
        $fields = $data;
        $fields['content'] = $fields['link'];
        unset($fields['link']);
        $fields['title'] = $fields['description'];
        unset($fields['description']);
        unset($fields['author']);
        $fields['is_external'] = 1;
        if (ArrayHelper::keyExists('config', $fields)) {
            $fields['config'] = Spyc::YAMLDump($fields['config']);
        }
        (new Query())->createCommand()->insert(self::TABLE, $fields)->execute();
        $fields['id'] = \Yii::$app->db->getLastInsertID();
        $module = new self($fields);
        $ret = $module->installModule();
        if (!$ret['status']) return $ret;

        return [
            'status' => true,
            'data'   => $module,
        ];
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
            if (self::isModuleName($name)) {
                $result = self::execInfo($name);
                if ($result['status']) {
                    if ($result['data']['version']) {
                        $ret[$id] = $result['data']['version'];
                    }
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
        $name = $this->content;
        if (self::isModuleName($name)) {
            $result = self::execUpgrade($name);
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
        } else {
            return [
                'status' => false,
                'data'   => 'Нет файла модуля',
            ];
        }
    }

    /**
     * Исполняет функцию инсталляции
     *
     * @param string $name название модуля
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public static function execInstall($name) {
        return self::_execInstall($name, self::TYPE_MODULE);
    }

    /**
     * Исполняет функцию обновления
     *
     * @param string $name название модуля
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public static function execUpgrade($name) {
        return self::_execUpgrade($name, self::TYPE_MODULE);
    }

    /**
     * Исполняет функцию  получения информации о модуле
     *
     * @param string $name название модуля
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    public static function execInfo($name) {
        return self::_execInfo($name, self::TYPE_MODULE);
    }

    /**
     * Проверяет в строке название модуля или нет
     * @return bool
     * true - имя является названием модуля
     * false - имя не является названием модуля
     */
    private static function isModuleName($string) {
        $arr = explode(' ', $string);
        if (count($arr) == 1) {
            if (strpos($string, 'mod_') !== false) return true;
            else return false;
        } else {
            return false;
        }
    }

}
