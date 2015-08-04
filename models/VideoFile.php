<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;

class VideoFile
{
    const TABLE = 'cms_user_video';

    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return VideoFile
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
     * @inheritdoc
     */
    public function getId()
    {
        return $this->fields['id'];
    }

    /**
     * Возвращает картинку опроса
     * @return string картинку опроса
     */
    public function getImage()
    {
        return '/images/video/' . $this->fields['video_img'];
    }

    /**
     * Удаляет видео
     *
     * @return boolean результат удаление
     *                 true - положительно
     *                 false - отрицательно
     */
    public function delete()
    {
        SuffraConfig::deleteFile($this->getImage());
        $query = new Query();
        $command = $query->createCommand();
        $command->delete(self::TABLE, ['id' => $this->getId()])->execute();

        return true;
    }

}
