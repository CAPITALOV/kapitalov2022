<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;

class Voting
{
    const TABLE = 'cms_goods';

    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return Voting
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
        return '/upload/goods/' . $this->fields['img'];
    }

    /**
     * Удаляет опрос, ответы на опрос, счетчик ответов, варианты ответов на опрос, результаты завершенного опроса
     *
     * @return boolean результат удаление
     *                 true - положительно
     *                 false - отрицательно
     */
    public function delete()
    {
        // удаляю картинку отчета
        SuffraConfig::deleteFile($this->getImage());
        $query = new Query();
        $command = $query->createCommand();
        // ответы
        $command->delete('cms_goods_votes', ['good_id' => $this->getId()])->execute();
        // варианты ответов на опрос
        $command->delete('cms_goods_querys', ['gid' => $this->getId()])->execute();
        // результаты завершенного опроса
        $command->delete('cms_goods_results', ['voting_id' => $this->getId()])->execute();
        // опрос
        $command->delete('cms_goods', ['id' => $this->getId()])->execute();

        return true;
    }

}
