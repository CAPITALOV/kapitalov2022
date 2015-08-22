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
use yii\helpers\ArrayHelper;
use yii\web\Response;

class CabinetController extends SuperadminBaseController
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

    public function actionStock_list()
    {
        return $this->render([
            'items' => Stock::query()->orderBy(['name' => SORT_ASC])->all(),
        ]);
    }

    /**
     * @param int $id идентификатор курса
     *
     * @return string
     */
    public function actionStock_item($id)
    {
        $item = \app\models\Stock::find($id);
        $defaultParams = [
            'start' => (new \DateTime())->sub(new \DateInterval('P30D'))
        ];
        if (!Yii::$app->user->identity->isPaid()) {
            $defaultParams['end'] = new \DateTime();
        }

        // график с продажами
        {
            $params = ArrayHelper::merge($defaultParams, [
                'rows'  => [
                    \app\models\StockKurs::query(['stock_id' => $id])->all(),
                ],
            ]);
            $lineArrayKurs = \app\service\GraphExporter::convert($params);
        }

        // график с прогнозом (красная линия)
        {
            $params = ArrayHelper::merge($defaultParams, [
                'rows'  => [
                    \app\models\StockPrognosisRed::query(['stock_id' => $id])
                        ->select([
                            'date',
                            'delta as kurs',
                        ])
                        ->all(),
                ],
            ]);
            $lineArrayRed = \app\service\GraphExporter::convert($params);
        }

        // график с прогнозом (синяя линия)
        {
            $params = ArrayHelper::merge($defaultParams, [
                'rows'  => [
                    \app\models\StockPrognosisBlue::query(['stock_id' => $id])
                        ->select([
                            'date',
                            'delta as kurs',
                        ])
                        ->all(),
                ],
            ]);
            $lineArrayBlue = \app\service\GraphExporter::convert($params);
        }

        return $this->render([
            'item'          => $item,
            'lineArrayKurs' => $lineArrayKurs,
            'lineArrayRed'  => $lineArrayRed,
            'lineArrayBlue' => $lineArrayBlue,
        ]);
    }

    public function actionPassword_change()
    {
        $model = new \app\models\Form\PasswordNew();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->action(\Yii::$app->user->identity)) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }
}
