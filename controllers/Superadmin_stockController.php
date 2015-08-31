<?php

namespace app\controllers;

use app\models\Stock;
use app\models\StockKurs;
use app\models\StockPrognosis;
use app\models\StockPrognosisBlue;
use app\models\StockPrognosisRed;
use cs\services\VarDumper;
use cs\web\Exception;
use Yii;
use yii\base\UserException;

class Superadmin_stockController extends SuperadminBaseController
{

    public function actionIndex()
    {
        $items = Stock::query()->all();
        $red = StockPrognosisRed::query()
            ->select([
                'stock_id',
                'MIN(`date`) as min',
                'MAX(`date`) as max',
            ])
            ->groupBy('stock_id')
            ->all();
        $blue = StockPrognosisBlue::query()
            ->select([
                'stock_id',
                'MIN(`date`) as min',
                'MAX(`date`) as max',
            ])
            ->groupBy('stock_id')
            ->all();
        $kurs = StockKurs::query()
            ->select([
                'stock_id',
                'MIN(`date`) as min',
                'MAX(`date`) as max',
            ])
            ->groupBy('stock_id')
            ->all();

        return $this->render([
            'items' => $items,
            'red'   => $red,
            'blue'  => $blue,
            'kurs'  => $kurs,
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

    /**
     * Импортирует прогнозы
     */
    public function actionImport($id)
    {
        $model = new \app\models\Form\StockPrognosisImport();
        if ($model->load(Yii::$app->request->post()) && $model->import($id)) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Импортирует курсы с finam
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     */
    public function actionImport_kurs($id)
    {
        $model = new \app\models\Form\StockKursImport();
        if ($model->load(Yii::$app->request->post()) && $model->import($id)) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
                'item' => Stock::find($id),
            ]);
        }
    }

    public function actionKurs_add($id)
    {
        $model = new \app\models\Form\StockKursAdd();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model'    => $model,
                'stock_id' => $id,
            ]);
        }
    }

    public function actionGraph($id)
    {
        return $this->render([
            'item' => \app\models\Stock::find($id),
        ]);
    }

    public function actionKurs_edit($id)
    {
        return $this->render([
            'items' => \app\models\StockKurs::query(['stock_id' => $id])->orderBy(['date' => SORT_DESC])->all(),
            'item'  => \app\models\Stock::find($id),
        ]);
    }

    /**
     * Обновляет по AJAX знчение
     *
     * @return \yii\web\Response
     */
    public function actionKurs_update()
    {
        $id = self::getParam('id');
        $value = self::getParam('value');
        $type = self::getParam('type');
        $item = StockKurs::find($id);
        switch ($type) {
            case 'date':
                $item->update(['date' => $value]);
                break;
            case 'kurs':
                $item->update(['kurs' => $value]);
                break;
        }

        return $this->jsonSuccess();
    }

    /**
     * Обновляет по AJAX знчение
     *
     * @return \yii\web\Response
     */
    public function actionPrognosis_update()
    {
        $id = self::getParam('id');
        $value = self::getParam('value');
        $type = self::getParam('type');
        $item = StockPrognosis::find($id);
        switch ($type) {
            case 'date':
                $item->update(['date' => $value]);
                break;
            case 'kurs':
                $item->update(['kurs' => $value]);
                break;
        }

        return $this->jsonSuccess();
    }

    public function actionPrognosis_edit($id)
    {
        return $this->render([
            'items' => \app\models\StockPrognosis::query(['stock_id' => $id])->orderBy(['date' => SORT_DESC])->all(),
            'item'  => \app\models\Stock::find($id),
        ]);
    }

    public function actionPrognosis_add($id)
    {
        $model = new \app\models\Form\StockPrognosisAdd();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model'    => $model,
                'stock_id' => $id,
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

    /**
     * Выводит форму и обрабатывает "Удаление прогноза"
     *
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionPrognosis_delete_red($id)
    {
        $model = new \app\models\Form\StockPrognosisRedDelete();
        if ($model->load(Yii::$app->request->post()) && $model->remove($id)) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму и обрабатывает "Удаление прогноза"
     *
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionPrognosis_delete_blue($id)
    {
        $model = new \app\models\Form\StockPrognosisBlueDelete();
        if ($model->load(Yii::$app->request->post()) && $model->remove($id)) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму и обрабатывает "Удаление курса"
     *
     * @param int $id идентификатор акции
     *
     * @return \yii\web\Response
     */
    public function actionKurs_delete($id)
    {
        $model = new \app\models\Form\StockKursDelete();
        if ($model->load(Yii::$app->request->post()) && $model->remove($id)) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render([
                'model' => $model,
            ]);
        }
    }
}
