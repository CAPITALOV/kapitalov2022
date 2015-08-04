<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\VarDumper;

/**
 * TopMenu
 */
class CronAction extends Model
{
    const TABLE = 'cms_cron_jobs';

    /** @var integer $id идентификатор записи */
    public $id;
    public $job_name;
    public $job_interval;
    public $job_run_date;
    public $component;
    public $model_method;
    public $custom_file;
    public $is_enabled;
    public $is_new;
    public $comment;
    public $class_name;
    public $class_method;


    /** @var array $fields все названия полей таблицы */
    private static $fields = [
        'job_name',
        'job_interval',
        'job_run_date',
        'component',
        'model_method',
        'custom_file',
        'is_enabled',
        'is_new',
        'comment',
        'class_name',
        'class_method',
    ];
    
    /** @var array $fields поля для добавления ['имя поля', 'значние по умолчанию, если значение равно нулю из формы']*/
    private static $fieldsAdd = [
    ['job_name'],
    ['job_interval'],
    ['job_run_date'],
    ['component',''],
    ['model_method', ''],
    ['custom_file', ''],
    ['is_enabled', 1],
    ['is_new', 1],
    ['comment'],
    ['class_name', ''],
    ['class_method', ''],
    ];
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [[
                'job_name',
                'job_interval',
                'is_enabled',
                'comment',
            ], 'required'],
            [[
                'job_interval',
            ], 'integer'],
            [[
                'comment',
                'custom_file',
                'class_name',
                'class_method',
            ], 'string', 'min' => 1, 'max' => 200],
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
            if (count($field) > 1) {
                if (is_null($this->$name)) {
                    $this->$name = $field[1];
                }
            }
            $fields[$name] = $this->$name;
        }
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
            $fields[$name] = $this->$name;
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
                $this->$key = $fields[$key];
            }
        }
    }

    /**
     * Ищет запись в таблице
     * @return static|null
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
}
