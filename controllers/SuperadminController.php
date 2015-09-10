<?php

/**
 * Класс для действий Superadmin
 *
 */

namespace app\controllers;

use app\models\Registration;
use app\models\Stock;
use app\models\StockKurs;
use app\models\StockPrognosisBlue;
use app\models\StockPrognosisRed;
use cs\services\VarDumper;
use Yii;
use app\service\CalculatingProbability;

class SuperadminController extends SuperadminBaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionReferal()
    {
        return $this->render();
    }

    /**
     * Удаление информации о регистрации по реферальной ссылке
     *
     * REQUEST:
     * - id - int - идентификатор cap_registration.id
     *
     * @return string
     */
    public function actionReferal_delete()
    {
        $id = self::getParam('id');
        $item = Registration::find($id);
        if (is_null($item)) {
            return self::jsonError('Нет такой записи');
        }
        $item->delete();

        return self::jsonSuccess();
    }

    public function actionCalc()
    {
        $blue =  CalculatingProbability::initStock(1,2);
        $red =  CalculatingProbability::initStock(1,1);
        VarDumper::dump([$blue->calc(),$red->calc()]);
    }
}
