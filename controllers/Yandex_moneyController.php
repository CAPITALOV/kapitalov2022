<?php

namespace app\controllers;

use app\models\User;
use cs\base\BaseController;
use cs\services\VarDumper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\web\Request;
use yii\web\Response;
use yii\widgets\ActiveForm;
use app\service\RegistrationDispatcher;
use app\service\PasswordRecoverDispatcher;
use cs\web\Exception;
use \YandexMoney\API;

class Yandex_moneyController extends BaseController
{
    public $layout = 'main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionAuth()
    {
        /** @var \app\service\authclient\YandexMoney $client */
        $client = Yii::$app->authClientCollection->getClient('yandex_money');
        $code = self::getParam('code');
        $access_token_response = API::getAccessToken($client->clientId, $code, 'http://capitalov.localhost/yandexMoney', $client->clientSecret);
        VarDumper::dump($access_token_response);
        if(property_exists($access_token_response, "error")) {
            // process error
        }
        $access_token = $access_token_response->access_token;
    }


    /**
     * Попытка сгенерировать auth_url перейти на него в новом окне
     */
    public function actionTest1()
    {
        /** @var \app\service\authclient\YandexMoney $client */
        $client = Yii::$app->authClientCollection->getClient('yandex_money');
        $auth_url = API::buildObtainTokenUrl($client->clientId, 'http://capitalov.localhost/yandexMoney', ['account-info']);

        return $this->render([
            'url' => $auth_url,
        ]);
    }
}
