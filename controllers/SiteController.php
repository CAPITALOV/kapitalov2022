<?php

/*
 * Класс для действий незерегистрированного пользователя
 *
 */

namespace app\controllers;

use app\models\Log;
use app\models\Stock;
use app\models\StockKurs;
use app\models\User;
use cs\services\Security;
use cs\services\Url;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
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
    public $layout = 'site';

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

        return $this->render('index');
    }

    public static function sendRequest($url, $options = [], $access_token = null)
    {
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Yandex.Money.SDK/PHP');
        curl_setopt($curl, CURLOPT_POST, 1);
        $query = http_build_query($options);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //curl_setopt($curl, CURLOPT_VERBOSE, 1);
//        curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, true);
//        curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 2);
//        curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
        $body = curl_exec($curl);

        $result = new \StdClass();
        $result->status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result->body = $body;
        curl_close($curl);

        \cs\services\VarDumper::dump($result) ;
    }

    protected static function processResult($result)
    {
        switch ($result->status_code) {
            case 400:
                break;
            case 401:
                break;
            case 403:
                break;
            default:
                if($result->status_code >= 500) {
                }
                else {
                    return json_decode($result->body);
                }
        }
    }

    public function getVideo($embedTag)
    {
        $x = new \DOMDocument();
        $x->loadXML('<?xml version="1.0" encoding="utf-8"?>'.$embedTag);
        $flashvars = $x->documentElement->getAttribute('flashvars');

        $vars = explode('&', $flashvars);
        foreach($vars as $i) {
            $new[] = explode('=',$i);
        }
        foreach($new as $i)
        {
            if ($i[0] == 'params') {
                $v = $i[1];
            }
        }
        $vars = json_decode(urldecode($v));
        $url = $vars->video_data[0]->hd_src;

        return Yii::$app->response->sendContentAsFile(file_get_contents($url),'video.mp4');
    }

    public function actionTime()
    {
        self::sendRequest('http://staging.capitalov.com/registration', [
            'Registration[email]'     => Security::generateRandomString(10) . '@gmail.com',
            'Registration[password1]' => '123',
            'Registration[password2]' => '123',
        ]);

    }

    public function actionAbout()
    {
        return $this->render();
    }

    public function actionPrice()
    {
        return $this->render();
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

    public function actionLog()
    {
        return $this->render([
            'log' => file_get_contents(Yii::getAlias('@runtime/logs/app.log')),
        ]);
    }

    public function actionLog_db()
    {
        $query = Log::query()->orderBy(['log_time' => SORT_DESC]);
        $category = self::getParam('category', '');
        if ($category) {
            $query->where(['like', 'category', $category . '%', false]);
        }
        $type = self::getParam('type', '');
        if ($type) {
            switch ($type) {
                case 'INFO':
                    $type = \yii\log\Logger::LEVEL_INFO;
                    break;
                case 'ERROR':
                    $type = \yii\log\Logger::LEVEL_ERROR;
                    break;
                case 'WARNING':
                    $type = \yii\log\Logger::LEVEL_WARNING;
                    break;
                case 'PROFILE':
                    $type = \yii\log\Logger::LEVEL_PROFILE;
                    break;
                default:
                    $type = null;
                    break;
            }
            if ($type) {
                $query->where(['type' => $type]);
            }
        }

        return $this->render([
            'dataProvider' => new ActiveDataProvider([
                'query'      => $query,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ])
        ]);
    }
}
