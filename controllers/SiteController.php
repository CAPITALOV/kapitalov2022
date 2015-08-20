<?php

/*
 * Класс для действий незерегистрированного пользователя
 *
 */

namespace app\controllers;

use app\models\Stock;
use app\models\StockKurs;
use app\models\User;
use cs\services\Url;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\data\ActiveDataProvider;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\UserForm;
use app\models\Form\UserPassword as FormUserPassword;

class SiteController extends \cs\base\BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'profile', 'profile_password_change'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile', 'profile_password_change'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post','get'],
                ],
            ],
        ];

    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'usersBlock' => 'app\controllers\Users\Block'
        ];
    }

    public function actionIndex()
    {
//        \cs\services\VarDumper::dump(
//            new Url('https://oauth.yandex.ru/authorize?client_id=B64E976FD0393C52F06BE7F6DA80983010F506D8B22B037E17458E154826D85B&response_type=code&redirect_uri=http%3A%2F%2Fc.galaxysss.ru%2Fauth%3Fauthclient%3Dyandex&xoauth_displayname=My%20Application')
//
//        );
        return $this->render('index');
    }

    public function actionImport()
    {
        $data = (new \app\service\DadaImporter\Finam())->import('2015-08-01');
        $stock_id = 1;
        // стратегия: Если данные есть то, они не трогаются
        $dateArray = ArrayHelper::getColumn($data, 'date');
        sort($dateArray);
        $rows = StockKurs::query(['between', 'date', $dateArray[0], $dateArray[count($dateArray)-1]])->andWhere(['stock_id' => $stock_id])->all();
        $dateArrayRows = ArrayHelper::getColumn($rows, 'date');
        $new = [];
        foreach($data as $row) {
            if (!in_array($row['date'], $dateArrayRows)) {
                $new[] = [
                     $stock_id,
                     $row['date'],
                     $row['kurs'],
                ];
            }
        }
        StockKurs::batchInsert(['stock_id', 'date', 'kurs'], $new);
        \cs\services\VarDumper::dump($new);
    }

    /*
     * Выводит страницу логина по запросу GET
     * и логинит пользователя если был запрос POST
     *
     *
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Выдает элементы поиска курсов для строки поиска Autocomplete
     */
    public function actionSearch_stock_autocomplete()
    {
        $term = self::getParam('term');

        return self::jsonSuccess(
            Stock::query(['like', 'name', $term . '%', false])->select('id, name as value')->all()
        );
    }

    /**
     * Выводит профиль пользователя
     */
    public function actionProfile()
    {
        return $this->render('profile', [
            'user' => User::findIdentity(
                Yii::$app->user->getId()
            ),
        ]);
    }

    /**
     * Выводит форму редактирования для новостной ленты и обновляет через POST
     */
    public function actionProfile_password_change()
    {
        $model = FormUserPassword::find(
            Yii::$app->user->getId()
        );
        if ($model->load(Yii::$app->request->post()) && $model->update()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            self::log('Поменял пароль себе');
            return $this->refresh();
        } else {
            return $this->render('profile_password_change', [
                'model' => $model,
            ]);
        }
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Логирует действие пользователя
     */
    public function log($description) {
        parent::logAction(Yii::$app->user->identity->id, $description);
    }
}
