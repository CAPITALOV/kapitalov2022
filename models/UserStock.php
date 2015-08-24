<?php

namespace app\models;

use app\models\NewsItem;
use app\models\User;
use app\services\GsssHtml;
use cs\services\Str;
use cs\services\VarDumper;
use Yii;
use yii\base\Model;
use cs\Widget\FileUpload2\FileUpload;
use yii\db\Query;
use yii\helpers\Html;

/**
 * Обслуживает таблицу cap_users_stock_buy, которая хранит информацию до какого времени была оплачена акция конкретного пользователя
 */
class UserStock extends \cs\base\DbRecord
{
    const TABLE = 'cap_users_stock_buy';

    /**
     * Возвращает временную метку до какого времени проплачена акция
     *
     * @param int $userId
     * @param int $stockId
     *
     * @return integer|false
     * false - если нет значения (не найдено)
     */
    public static function getDateFinish($userId, $stockId)
    {
        return self::query([
            'user_id' => $userId,
            'stock_id' => $stockId,
        ])->select(['date_finish'])->scalar();
    }

    /**
     * Оплачена акиця на настоящий момент?
     *
     * @param int $userId
     * @param int $stockId
     *
     * @return bool
     */
    public static function isPaid($userId, $stockId)
    {
        $datetime = self::getDateFinish($userId, $stockId);
        if ($datetime === false) return false;

        return $datetime >= time();
    }
}
