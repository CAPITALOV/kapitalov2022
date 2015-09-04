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
 */
class Stock extends \cs\base\DbRecord
{
    const TABLE = 'cap_stock';

    public function getName()
    {
        return $this->getField('name', '');
    }

    public function getImage()
    {
        return $this->getField('logo', '');
    }

    /**
     * Возвращает акции которые оплаченые
     *
     * @return \yii\db\Query
     */
    public static function getPaid()
    {
        return self::query()
            ->select([
                'cap_stock.id',
                'cap_stock.name',
            ])
            ->innerJoin('cap_users_stock_buy', 'cap_users_stock_buy.stock_id = cap_stock.id and cap_users_stock_buy.user_id = :uid', [':uid' => Yii::$app->user->id])
            ->orderBy(['cap_stock.name' => SORT_ASC])
            ;
    }

    /**
     * Возвращает акции которые оплачены
     * @return \yii\db\Query
     */
    public static function getNotPaid()
    {
        return self::query()
            ->select([
                'cap_stock.id',
                'cap_stock.name',
            ])
            ->leftJoin('cap_users_stock_buy', 'cap_users_stock_buy.stock_id = cap_stock.id and cap_users_stock_buy.user_id = :uid', [':uid' => Yii::$app->user->id])
            ->orderBy(['cap_stock.name' => SORT_ASC])
            ->where(['cap_users_stock_buy.stock_id' => null])
            ;
    }
}
