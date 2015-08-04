<?php
/**
 * BaseController
 */

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\helpers\StringHelper;
use yii\web\Controller;

class BaseController extends Controller
{
    /**
     * Возвращает стандартный ответ JSON при положительном срабатывании
     * https://redmine.suffra.com/projects/suffra/wiki/Стандартный_ответ_JSON
     * @param mixed $data [optional] возвращаемые данные
     * @return string JSON
     */
    public function jsonSuccess($data = null)
    {
        if (is_null($data)) $data = 'ok';
        return self::json(['success' => $data]);
    }

    /**
     * Возвращает ответ контроллера
     *
     * @param mixed $data возвращаемые данные
     *                    [
     *                    'status' => boolean
     *                    'data' => данные при положительном срабатывании или сообщение об ошибке
     *                    ]
     *
     * @return string JSON
     */
    public function jsonController($data) {
        $dataC = null;
        if ($data['status']) {
            if (isset($data['data'])) {
                $dataC = $data['data'];
            }

            return $this->jsonSuccess($dataC);
        } else {
            if (isset($data['data'])) {
                $dataC = $data['data'];
            }

            return $this->jsonError($dataC);
        }
    }

    /**
     * Возвращает стандартный ответ JSON при отрицательном срабатывании
     * https://redmine.suffra.com/projects/suffra/wiki/Стандартный_ответ_JSON
     * @param mixed $data [optional] возвращаемые данные
     * @return string JSON
     */
    public function jsonError($data = null)
    {
        if (is_null($data)) $data = '';
        return self::json(['error' => $data]);
    }

    /**
     * Закодировать в JSON
     * @return string json string
     * */
    public static function json($array)
    {
        header('Content-type: application/json; charset=utf-8');
        return json_encode($array, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Логирует действие пользователя
     */
    public static function logAction($userId, $description) {
        $query = new Query();
        $command = $query->createCommand();
        $command->insert('adm_log', [
            'user_id'     => $userId,
            'description' => $description,
            'datetime'    => gmdate('YmdHis'),
        ])->execute();
    }


    /**
     * Вызов возможен как render($view, $params)
     * или как render($params)
     * тогда $view = название функции action
     * например если вызов произошел из метода actionOrders то $view = 'orders'
     *
     * @param string|array $view   шаблон или параметры шаблона
     * @param array        $params параметры шаблона если $view = шаблон, иначе не должен указываться
     *
     * @return string = \yii\base\Controller::render()
     */
    public function render($view = '', $params = [])
    {
        if (is_array($view)) {
            $params = $view;
            $view = strtolower(str_replace('action', '', debug_backtrace(2)[1]['function']));
        } else if ($view == '') {
            $params = [];
            $view = strtolower(str_replace('action', '', debug_backtrace(2)[1]['function']));
        } else if ($view == '.tpl') {
            $view = strtolower(str_replace('action', '', debug_backtrace(2)[1]['function'])) . '.tpl';
        }
        if (StringHelper::endsWith(strtolower($view) , '.tpl')) {
            $this->layout .= '.tpl';
        }

        if (self::getParam('_view', '') != '') {
            \cs\services\VarDumper::dump($params, 10);

            return '';
        }

        return parent::render($view, $params);
    }

    /**
     * Возвращает переменную из REQUEST
     *
     * @param string $name    имя переменной
     * @param mixed  $default значние по умолчанию [optional]
     *
     * @return string|null
     * Если такой переменной нет, то будет возвращено null
     */
    public static function getParam($name, $default = null)
    {
        $vGet = \Yii::$app->request->get($name);
        $vPost = \Yii::$app->request->post($name);
        $value = (is_null($vGet)) ? $vPost : $vGet;

        return (is_null($value)) ? $default : $value;
    }

} 