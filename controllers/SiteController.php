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
        return $this->getVideo('<embed type="application/x-shockwave-flash" src="https://fbstatic-a.akamaihd.net/rsrc.php/v1/yk/r/mMt7DDqIne4.swf" width="470" height="470" style="display: block;" id="swf_id_55ef1b6d785f24a18839569" name="swf_id_55ef1b6d785f24a18839569" bgcolor="#000000" quality="high" allowfullscreen="true" allowscriptaccess="always" salign="tl" scale="noscale" wmode="opaque" flashvars="params=%7B%22auto_hd%22%3Afalse%2C%22autoplay_reason%22%3A%22unknown%22%2C%22default_hd%22%3Afalse%2C%22disable_native_controls%22%3Atrue%2C%22inline_player%22%3Afalse%2C%22pixel_ratio%22%3A1%2C%22preload%22%3Atrue%2C%22start_muted%22%3Afalse%2C%22video_data%22%3A%5B%7B%22hd_src%22%3A%22https%3A%5C%2F%5C%2Fvideo.xx.fbcdn.net%5C%2Fhvideo-xfp1%5C%2Fv%5C%2Ft42.4659-2%5C%2F11993413_817464165033767_1413050955_n.mp4%3Foh%3Dc11e9aa9db7d444c3b160ec834898bb1%26oe%3D55EF39F8%22%2C%22is_hds%22%3Afalse%2C%22stream_type%22%3A%22progressive%22%2C%22is_live_stream%22%3Afalse%2C%22rotation%22%3A0%2C%22sd_src%22%3A%22https%3A%5C%2F%5C%2Fvideo.xx.fbcdn.net%5C%2Fhvideo-xfp1%5C%2Fv%5C%2Ft42.4659-2%5C%2F11993413_817464165033767_1413050955_n.mp4%3Foh%3Dc11e9aa9db7d444c3b160ec834898bb1%26oe%3D55EF39F8%22%2C%22video_id%22%3A%22817464161700434%22%2C%22sd_tag%22%3A%22%22%2C%22hd_tag%22%3A%22%22%2C%22spherical_hd_src%22%3Anull%2C%22spherical_hd_tag%22%3Anull%2C%22spherical_sd_src%22%3Anull%2C%22spherical_sd_tag%22%3Anull%2C%22projection%22%3A%22flat%22%2C%22subtitles_src%22%3Anull%7D%5D%2C%22show_captions_default%22%3Afalse%2C%22persistent_volume%22%3Atrue%2C%22hide_controls_when_finished%22%3Afalse%2C%22buffer_length%22%3A0.1%7D&amp;width=470&amp;height=470&amp;user=100001742075258&amp;log=no&amp;div_id=id_55ef1b6d785f24a18839569&amp;swf_id=swf_id_55ef1b6d785f24a18839569&amp;browser=Chrome+45.0.2454.85&amp;tracking_domain=https%3A%2F%2Fpixel.facebook.com&amp;post_form_id=&amp;string_table=https%3A%2F%2Fs-static.ak.facebook.com%2Fflash_strings.php%2Ft100120%2Fru_RU"/>');
        self::sendRequest('http://capitalov.com/registration', [
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
