<?php


namespace app\service\authclient;

use yii\authclient\OAuth2;
use yii\helpers\VarDumper;

/**
 * YandexMoney allows authentication via Yandex OAuth.
 *
 * In order to use Yandex OAuth you must register your application at <https://oauth.yandex.ru/client/new>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'yandex' => [
 *                 'class' => 'yii\authclient\clients\YandexMoney',
 *                 'clientId' => 'yandex_client_id',
 *                 'clientSecret' => 'yandex_client_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see    https://oauth.yandex.ru/client/new
 * @see    http://api.yandex.ru/login/doc/dg/reference/response.xml
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since  2.0
 */
class YandexMoney extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://sp-money.yandex.ru/oauth/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://sp-money.yandex.ru/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://login.yandex.ru';


    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('info', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function apiInternal($accessToken, $url, $method, array $params, array $headers)
    {
        if (!isset($params['format'])) {
            $params['format'] = 'json';
        }
        $params['oauth_token'] = $accessToken->getToken();

        return $this->sendRequest($method, $url, $params, $headers);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'yandex_money';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'YandexMoney';
    }

    public function auth22()
    {
        $defaultParams = [
            'client_id'     => $this->clientId,
            'response_type' => 'code',
            'redirect_uri'  => 'http://c.galaxysss.ru/yandexMoney',
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        VarDumper::dump($this->sendRequest('POST', $this->authUrl, $defaultParams, []));
    }
}
