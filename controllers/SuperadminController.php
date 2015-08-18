<?php

/**
 * Класс для действий Superadmin
 *
 */

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\User;
use app\models\AdminUserForm;
use app\controllers\BaseController;
use app\models\Component;
use app\models\Module;
use app\models\Plugin;
use app\models\TopMenu;
use app\models\CronAction;
use app\models\UserMenu;
use app\models\Form\Settings as FormSettings;
use app\models\Form\CronAction as FormCronAction;
use app\models\Form\UserMenu as FormUserMenu;
use app\models\Form\TopMenu as FormTopMenu;
use app\models\Form\Module as FormModule;
use app\models\Form\Component as FormComponent;
use app\models\Form\Plugin as FormPlugin;
use app\models\JsLoggerItem;
use Suffra\Config as SuffraConfig;
use app\service\CheckFiles;

class SuperadminController extends SuperadminBaseController
{
    const MEMCACHE_KEY_ALL_ITEMS = '\cmsCore::getAll/items';
    
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Выводит форму добавления пользователя и добавляет через POST
     */
    public function actionUsers_add()
    {
        $model = new AdminUserForm();
        if ($model->load(Yii::$app->request->post()) && $model->insert()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Добавил пользователя административной панели');

            return $this->refresh();
        } else {
            return $this->render('users_add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выводит форму редактирования пользователя и обновляет через POST
     */
    public function actionUsers_edit($id)
    {
        $model = AdminUserForm::find($id);
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Отредактировал пользователя административной панели');

            return $this->refresh();
        } else {
            return $this->render('users_edit', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Удалает пользователя админки
     */
    public function actionUsers_delete($id)
    {
        $user = User::findIdentity($id);
        $userId = $user->id;
        $userName = $user->username;
        $user->delete();
        self::log('Удалил пользователя id=' . $userId . ' name=' . $userName);

        return $this->jsonSuccess();
    }

}
