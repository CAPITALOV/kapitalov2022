<?php

namespace app\controllers;

use app\models\ChatMessage;
use app\models\Stock;
use app\models\StockKurs;
use app\models\StockPrognosis;
use app\models\StockPrognosisBlue;
use app\models\StockPrognosisRed;
use app\models\UserStock;
use cs\services\VarDumper;
use cs\web\Exception;
use Yii;
use yii\base\UserException;

class Superadmin_userstockController extends SuperadminBaseController
{
    /**
     * Показывает все чаты
     */
    public function actionIndex()
    {
        $users = UserStock::query()
            ->select([
                'cap_users_stock_buy.user_id',
                'cap_users.name_first',
                'cap_users.name_last',
                'cap_users.email',
                'cap_users.avatar',
            ])
            ->groupBy(['cap_users_stock_buy.user_id'])
            ->innerJoin('cap_users', 'cap_users.id = cap_users_stock_buy.user_id')
            ->all();

        for ($i = 0; $i < count($users); $i++) {
            $item = &$users[$i];

            $item['stockList'] = UserStock::query()
                ->select([
                    'cap_stock.id',
                    'cap_stock.name',
                    'cap_stock.logo',
                    'cap_stock.description',
                    'cap_users_stock_buy.date_finish',
                ])
                ->andWhere(['cap_users_stock_buy.user_id' => $item['user_id']])
                ->andWhere(['>', 'cap_users_stock_buy.date_finish', Yii::$app->formatter->asDate(time(), 'php:Y-m-d')])
                ->innerJoin('cap_stock', 'cap_stock.id = cap_users_stock_buy.stock_id')
                ->orderBy(['cap_stock.name' => SORT_ASC])
                ->all();
        }

        return $this->render([
            'users'  => $users,
        ]);
    }

}
