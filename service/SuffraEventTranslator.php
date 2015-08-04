<?php

namespace app\service;

use Guzzle\Http\Client;

/**
 * Class will translate provided event data to suffra instant plugin  which will
 * populate event through internal mechanisms
 */
class SuffraEventTranslator {

    protected $endpointUrl;

    public function __construct() {
        $this->endpointUrl = (YII_ENV != 'prod' ? 'http://suffra.dev/' : 'https://suffra.com/') . 'event/translator';
    }

    /**
     * @todo write checks
     * @param array $eventData [ 
     *                      'name' (str) - event name,
     *                      'data' (str) - event data,
     *                             ]
     */
    public function translate($eventData) {
        $response = (new Client(['verify' => false]))->createRequest('POST', $this->endpointUrl, [
                    'Authorization' => 'Basic ' . base64_encode('suffra_admin:' . sha1(md5('suffra_admin' . $eventData['name'])))
                        ], ['event' => json_encode($eventData, JSON_UNESCAPED_UNICODE)])->send();
        
        \Yii::info(sprintf('Translated event %s to url: %s and got response: %s', $eventData['name'], $this->endpointUrl, $response->getBody()));

        return $response->getBody();
    }

}
