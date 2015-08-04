<?php

namespace app\models\Form;

use Suffra\Service\Debug;
use yii\db\Query;
use Suffra\Config as SuffraConfig;
use \Spyc;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class InstallObject extends \yii\base\Model {
    const TYPE_COMPONENT = 1;
    const TYPE_MODULE = 2;
    const TYPE_PLUGIN = 3;

    /**
     * Исполняет массив SQL комманд
     *
     * @param array $array массив SQL комманд
     *
     * @return array массив если были ошибки
     *
     */
    protected static function executeSqlArray($array) {
        $errors = [];
        $query = new Query();
        $command = $query->createCommand();
        foreach ($array as $sql) {
            try {
                $command->setSql($sql)->execute();
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return $errors;
    }

    private static function convertType($type) {
        switch ($type) {
            case self::TYPE_COMPONENT:
                return ['components','component'] ;
            case self::TYPE_PLUGIN:
                return ['plugins','plugin'] ;
            case self::TYPE_MODULE:
                return ['modules','module'] ;
        }
    }

    /**
     * Исполняет функцию info
     *
     * @param string $name название модуля
     * @param int    $type self::TYPE_*
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    protected static function _execInfo($name, $type) {
        return self::_execFunction($name, $type, 'info');
    }

    /**
     * Исполняет функцию install
     *
     * @param string $name название модуля
     * @param int    $type self::TYPE_*
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    protected static function _execInstall($name, $type) {
        return self::_execFunction($name, $type, 'install');
    }

    /**
     * Исполняет функцию upgrade
     *
     * @param string $name название модуля
     * @param int    $type self::TYPE_*
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    protected static function _execUpgrade($name, $type) {
        return self::_execFunction($name, $type, 'upgrade');
    }

    /**
     * Исполняет функцию инсталляции
     *
     * @param string $name название модуля
     * @param int    $type self::TYPE_*
     *
     * @return array [
     * 'status' boolean результат выполнения операции
     * 'data' mixed возвращенное значени или строка ошибки если status = false
     * ]
     */
    protected static function _execFunction($name, $type, $function) {
        $type = self::convertType($type);
        $type_str1 = $type[0];
        $type_str2 = $type[1];
        $basePath = SuffraConfig::getBasePath();
        $path = $basePath . '/' . $type_str1 . '/' . $name . '/install.php';
        if (file_exists($path)) {
            require_once $path;
            $functionName = $function . '_' . $type_str2 . '_' . $name;

            return [
                'status' => true,
                'data'   => call_user_func($functionName),
            ];
        } else {
            return [
                'status' => false,
                'data'   => 'Нет конфигурационного файла',
            ];
        }
    }

    /**
     * Обновляет конфиг плагина с сохранением Yaml в БД
     *
     * @param array $config
     * [
     * 'add' = [
     * 'name1' => 'value1',
     * 'name2' => 'value2',
     * ]
     * 'delete' = [
     * 'name3',
     * 'name4',
     * ]
     * ]
     */
    public function executeConfigArray($config) {
        $configThis = Spyc::YAMLLoadString($this->config);
        $isUpdate = false;
        $delete = ArrayHelper::getValue($config, 'delete', []);
        if (count($delete) > 0) {
            foreach ($delete as $key) {
                unset($configThis[ $key ]);
            }
            $isUpdate = true;
        }
        $add = ArrayHelper::getValue($config, 'add', []);
        if (count($add) > 0) {
            foreach ($add as $key => $value) {
                $configThis[ $key ] = $value;
            }
            $isUpdate = true;
        }
        if ($isUpdate) {
            (new Query())->createCommand()->update(static::TABLE, [
                'config' => Spyc::YAMLDump($configThis)
            ], ['id' => $this->id])->execute();
        }
    }

    /**
     * Из массива всех обновлений выбирает те которые нужны для обновления
     *
     * @param array $updateAll весь массив обновлений из функции обновления компонента
     *                         return [
     *                         '0.1' => [],
     *                         '0.1.1' => [],
     *                         '0.2' => [],
     *                         '1.0' => [],
     *                         '1.1' => [],
     *                         '1.4' => [],
     *                         '1.7' => [],
     *                         '1.8' => [],
     *                         '2.0' => [],
     *                         '2.0.3' => [],
     *                         '2.0.3.34' => [],
     *                         '2.0.5' => [],
     *                         '2.0.60' => [],
     *                         '2.0.65' => [],
     *                         ];
     *
     * @return array массив для обновления
     *               например если 'version' = '1.1' и 'version_dev' = '2.0.65' то будет возвращено
     *       [
     * '1.4' => [],
     * '1.7' => [],
     * '1.8' => [],
     * '2.0' => [],
     * '2.0.3' => [],
     * '2.0.3.34' => [],
     * '2.0.5' => [],
     * '2.0.60' => [],
     * '2.0.65' => [],
     * ];
     */
    protected function getUpdateArray($updateAll) {
        $version = $this->version;
        $return = [];

        $versions = array_keys($updateAll);
        // нахожу какие индексы больше чем $version
        foreach ($updateAll as $v => $data) {
            if (version_compare($v, $version) > 0) {
                $return[] = $data;
            }
        }

        return $return;
    }

} 