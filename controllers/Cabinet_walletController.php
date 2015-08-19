<?php

/**
 * Класс для действий Superadmin
 *
 */

namespace app\controllers;

use app\models\Stock;
use cs\services\VarDumper;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\filters\AccessControl;
use yii\web\Response;

class Cabinet_walletController extends SuperadminBaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render([
            'end' => Yii::$app->user->identity->getField('paid_time'),
        ]);
    }

}
