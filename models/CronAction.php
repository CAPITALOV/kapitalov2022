<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;

class CronAction
{
    const TABLE = 'cms_cron_jobs';

    public $id;
    public $fields;

    public function __construct($fields) {
        $this->id = $fields['id'];
        $this->fields = $fields;
    }

    /**
     * Ищет строку по идентификатору
     * @param integer $id  идентификатор строки
     * @return CronAction
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
     * Выдает идентификатор элемента
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Удаляет строку
     */
    public function delete() {
        $query = new Query();
        $command = $query->createCommand();
        return $command->delete(self::TABLE, ['id' => $this->getId() ])->execute();
    }

    /**
     * Исполняет комманду
     * @return string лог исполнения функции выводимый при помощи echo
     */
    public function execute() {
        $http = curl_init();

        $url = SuffraConfig::createFullUrl('/cronOne.php?id=' . $this->getId() . '&pass=e10adc3949ba59abbe56e057f20f883e');
        curl_setopt($http, CURLOPT_URL,  $url);
        curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($http);
        $info = curl_getinfo($http);
        curl_close($http);

        return [
            'info' => $info,
            'output' => $output,
        ];
    }
}
