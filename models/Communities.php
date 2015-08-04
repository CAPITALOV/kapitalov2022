<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;

class Communities extends \cs\base\DbRecord
{
    const TABLE = 'cms_communities_category';

    /**
     * Обновляет сортировку
     *
     * @param array $ids [1,2,3, ...] массив идентификаторов строк по возрастанию сортировки
     *
     */
    public static function resort($ids)
    {
        $query = new Query();
        $command = $query->createCommand();
        $c = 1;
        foreach ($ids as $id) {
            $command->update(self::TABLE, [
                'order' => $c
            ], ['id' => $id])->execute();
            $c++;
        }
    }

}
