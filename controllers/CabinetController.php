<?php

/**
 * Класс для действий кабинета
 *
 */

namespace app\controllers;

use app\models\Stock;
use app\models\UserStock;
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

    /**
     * Ajax
     * Выдает значения для графика курса по заданному диапазону
     *
     * REQUEST:
     * - id - int - идентификатор курса
     * - min - string - дата начала графика 'yyyy-mm-dd'
     * - max - string - дата окончания графика 'yyyy-mm-dd'
     *
     * @return string json
     *                {
     *                   'red': - object - data которая для графика нужна
     *                   'blue': - object - data которая для графика нужна
     *                   'kurs': - object - data которая для графика нужна
     *                }
     */
    public function actionGraph_ajax()
    {
        $start = self::getParam('min');
        $end = self::getParam('max');
        $id = self::getParam('id');

        $defaultParams = [
            'start' => new \DateTime($start),
            'end'   => new \DateTime($end),
        ];

        // график с продажами
        {
            $params = ArrayHelper::merge($defaultParams, [
                'rows'  => [
                    \app\models\StockKurs::query(['stock_id' => $id])
                        ->andWhere(['between', 'date', $start, $end])
                        ->select(['date', 'kurs'])
                        ->all(),
                ],
            ]);
            $lineArrayKurs = \app\service\GraphExporter::convert($params);
        }

        $graph3 = new \cs\Widget\ChartJs\Line([
            'width'     => 800,
            'lineArray' => $lineArrayKurs,
        ]);

        return self::jsonSuccess([
            'red'  => [],
            'blue' => [],
            'kurs' => $graph3->getData(),
        ]);
    }

    public function actionStock_list()
    {
        $items = Stock::query()->orderBy(['name' => SORT_ASC])->all();
        $dateFinishList = UserStock::query(['user_id' => \Yii::$app->user->getId()])->select([
            'stock_id',
            'date_finish',
        ])->all();
        for($i=0;$i<count($items);$i++) {
            $item = &$items[$i];
            foreach($dateFinishList as $row) {
                if ($row['stock_id'] == $item['id']) {
                    $item['date_finish'] = $row['date_finish'];
                }
            }
            if (!isset($item['date_finish'])) {
                $item['date_finish'] = null;
            }
        }

        return $this->render([
            'items' => $items,
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
        $start = (new \DateTime())->sub(new \DateInterval('P30D'));
        $isPaid = Yii::$app->user->identity->isPaid($id);
        if ($isPaid) {
            $end = (new \DateTime())->add(new \DateInterval('P30D'));
        } else {
            $end = (new \DateTime());
        }
        $defaultParams = [
            'start' => $start,
            'end'   => $end,
        ];

        // график с продажами
        {
            $params = ArrayHelper::merge($defaultParams, [
                'rows'  => [
                    \app\models\StockKurs::query(['stock_id' => $id])
                        ->andWhere(['between', 'date', $start->format('Y-m-d'), $end->format('Y-m-d')])
                        ->all(),
                ],
            ]);
            $lineArrayKurs = \app\service\GraphExporter::convert($params);
        }

        // график с прогнозом (красная линия)
        {
            $params = ArrayHelper::merge($defaultParams, [
                'rows'  => [
                    \app\models\StockPrognosisRed::query(['stock_id' => $id])
                        ->andWhere(['between', 'date', $start->format('Y-m-d'), $end->format('Y-m-d')])
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
                        ->andWhere(['between', 'date', $start->format('Y-m-d'), $end->format('Y-m-d')])
                        ->select([
                            'date',
                            'delta as kurs',
                        ])
                        ->all(),
                ],
            ]);
            $lineArrayBlue = \app\service\GraphExporter::convert($params);
        }

        // union
        {
            $lineArrayUnion = \app\service\GraphUnion::convert([
                'x' => $lineArrayRed['x'],
                'y' => [
                    $lineArrayRed['y'][0],
                    $lineArrayBlue['y'][0],
                ]
            ]);
        }

        // union2
        {
            $lineArrayUnion2 = \app\service\GraphUnion::convert([
                'x' => $lineArrayRed['x'],
                'y' => [
                    $lineArrayRed['y'][0],
                    $lineArrayBlue['y'][0],
                    $lineArrayKurs['y'][0],
                ]
            ]);
        }

        return $this->render([
            'item'           => $item,
            'lineArrayKurs'  => $lineArrayKurs,
            'lineArrayRed'   => $lineArrayRed,
            'lineArrayBlue'  => $lineArrayBlue,
            'lineArrayUnion' => $lineArrayUnion,
            'lineArrayUnion2' => $lineArrayUnion2,
            'isPaid'         => $isPaid,
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
