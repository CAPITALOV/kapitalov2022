<?php

namespace app\controllers;

use app\models\Stock;
use cs\services\VarDumper;
use cs\web\Exception;
use Yii;
use yii\base\UserException;

class Superadmin_stockController extends SuperadminBaseController
{

    public function actionIndex()
    {
        return $this->render([
            'items' => Stock::query()->all(),
        ]);
    }

    public function actionAdd()
    {
        $model = new \app\models\Form\Stock();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    public function actionEdit($id)
    {
        $model = \app\models\Form\Stock::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    public function actionDelete($id)
    {
        \app\models\Form\Stock::find($id)->delete();

        return self::jsonSuccess();
    }
}
