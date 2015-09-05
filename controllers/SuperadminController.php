<?php

/**
 * Класс для действий Superadmin
 *
 */

namespace app\controllers;

use app\models\Registration;
use Yii;

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
}
