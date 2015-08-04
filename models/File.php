<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;

class File
{
    const TABLE = 'cms_user_files';

    const CATEGORY_IMAGE = 3;

    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return File
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
     * Возвращает пути ко всем файлам объекта File
     */
    private function getPathArray() {
        $return = [];

        $path = SuffraConfig::userDirectory($this->fields['user_id']);
        $return[] = $path . 'files/' . $this->fields['rand'];
        if ($this->fields['category_file'] == self::CATEGORY_IMAGE) {
            $return[] = $path . 'files/small/prev_' . $this->fields['prev'];
            $return[] = $path . 'files/small/thumb_' . $this->fields['prev'];
        }

        return $return;
    }

    /**
     * Удаляет физически все файлы объекта File
     */
    private function deleteAllObjects() {
        $objects = $this->getPathArray();
        foreach($objects as $file) {
            SuffraConfig::deleteFile($file);
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->fields['id'];
    }

    /**
     * Удаляет файл
     */
    public function delete()
    {
        $this->deleteAllObjects();
        $query = new Query();
        $command = $query->createCommand();
        $command->delete(self::TABLE, ['id' => $this->getId()])->execute();

        return true;
    }


}
