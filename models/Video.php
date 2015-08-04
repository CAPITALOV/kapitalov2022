<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class Video
{
    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    /**
     * Ищет пользователя по идентификатору
     *
     * @param integer $id идентификатор пользователя
     *
     * @return Video
     */
    public static function find($id)
    {
        $query = new Query();
        $row = $query->select('*')->from('cms_user_video')->where(['id' => $id])->one();
        if (!$row) {
            return null;
        } else {
            return new self($row);
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
     * Удаляет файл
     */
    public function delete()
    {
//        $path = $_SERVER['DOCUMENT_ROOT'] . $this->fields['filename'];
//        if (file_exists($path)) {
//            unlink($path);
//        }

        return true;
    }

    /**
     * блокирует файл
     */
    public function block()
    {
//        $path = $_SERVER['DOCUMENT_ROOT'] . $this->fields['filename'];
//        if (file_exists($path)) {
//            unlink($path);
//        }

        return true;
    }
}
