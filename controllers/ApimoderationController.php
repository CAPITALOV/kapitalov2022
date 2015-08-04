<?php

namespace app\controllers;

use app\controllers\BaseController;
use app\events\SuffraEvents;
use app\models\ModerationManager;
use app\models\ModerationObject;
use app\models\Translator as T;
use app\service\SuffraEventTranslator;
use DateInterval;
use DateTime;
use DateTimeZone;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\Cors;
use yii\web\Response;

class ApimoderationController extends BaseController {
    /* @todo check security for api */

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?', '@'],
                        'verbs' => ['POST', 'GET']
                    ],
                ],
            ],
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                ],
            ],
        ];
    }

    public function init() {
        Yii::$app->response->format = Response::FORMAT_JSON;
    }

    public function actionStats_chart() {
        if (!Yii::$app->request->getIsAjax()) {
            return new Response([
                'format' => 'json',
                'data' => [
                    'success' => false,
                    'error' => [
                        'id' => 1,
                        'message' => 'Not an Ajax request'
                    ]
                ]
            ]);
        }

        $m = Yii::$app->request->post('m');
        $d = Yii::$app->request->post('d');
        $Y = Yii::$app->request->post('Y');
        $type = Yii::$app->request->post('t');

        $manager = (new ModerationManager())->setReturnQueryInstance(true);
        switch ($type) {
            case 'month':
                /* fill up data by days  */
                $labels = range(1, cal_days_in_month(CAL_GREGORIAN, $m, $Y));
                $data = [];
                foreach ($labels as $day) {
                    $from = new DateTime();
                    $from->setTimezone(new DateTimeZone('UTC'));
                    $from->setTimestamp(mktime(0, 0, 0, $m, $day, $Y));
                    $to = clone $from;
                    $to->add(new DateInterval('P1D'));
                    $actionsQ = $manager->getStatisticsByCriteria([
                        'user_id' => $this->user->identity->id,
                        'from' => $from,
                        'to' => $to
                    ]);

                    $data[] = (int) $actionsQ->count();
                }
                break;
            case 'day':
                $labels = range(1, 24);
                foreach ($labels as $hour) {
                    $from = new DateTime();
                    $from->setTimezone(new DateTimeZone('UTC'));
                    $from->setTimestamp(mktime($hour, 0, 0, $m, $d, $Y));
                    $to = clone $from;
                    $to->add(new DateInterval('PT1H'));
                    $actionsQ = $manager->getStatisticsByCriteria([
                        'user_id' => $this->user->identity->id,
                        'from' => $from,
                        'to' => $to
                    ]);

                    $data[] = (int) $actionsQ->count();
                }
                break;

            default:
                break;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => "mod_statistics",
                    'fillColor' => "rgba(220,220,220,0.2)",
                    'strokeColor' => "rgba(220,220,220,1)",
                    'pointColor' => "rgba(220,220,220,1)",
                    'pointStrokeColor' => "#fff",
                    'pointHighlightFill' => "#fff",
                    'pointHighlightStroke' => "rgba(220,220,220,1)",
                    'data' => $data
                ]
        ]];
    }

    /**
     * @todo check for 
     * @param string $event json encoded array form Suffra site
     * ['name' => event name
     *  'data' => event data]
     */
    public function actionEvent_listener() {
        if (!$event = json_decode(Yii::$app->request->post('event', 'false'), JSON_UNESCAPED_UNICODE)) {
            return [
                'success' => false,
                'error' => [
                    'id' => 1,
                    'message' => 'Bad request'
            ]];
        }

        /* check authorization */
        if (!isset($_SERVER['PHP_AUTH_PW']) || sha1(md5($_SERVER['PHP_AUTH_USER'] . $event['name'])) != $_SERVER['PHP_AUTH_PW']) {
            return [
                'success' => false,
                'error' => [
                    'id' => 1,
                    'message' => 'Unauthorized'
            ]];
        }

        $mm = new ModerationManager();

        $eventName = $event['name'];
        $eventData = $event['data'];
        $modObjData = [];

        switch ($eventName) {
            case SuffraEvents::ACTION_VIOLATION_TO_MODERATOR:
                $mod_id = $eventData['mod_id'];
                $uid = $eventData['uid'];
                $message = ['msg'];

                if ($mm->addModeratorViolation($mod_id, $uid, $message)) {
                    $result = ['success' => true, 'error' => false];
                    /* add penalty */
                    $mm->changeRating($mod_id, $mm->getSetting('Moderator violation cost', -10));
                } else {
                    $result = ['success' => false, 'error' => ['id' => 1, 'message' => 'Error saving violation']];
                }

                return $result;
                break;
            case SuffraEvents::ACTION_VIOLATION:
                $oid = $eventData['oid'];
                $uid = $eventData['uid'];
                $type = $eventData['type'];
                $message = $eventData['m'];

                $modObjData = [];

                $modObjData['type'] = ModerationObject::getInternalType($type);

                $modObjData['foreign_table'] = ModerationObject::$typesToTables[$modObjData['type']];

                /* handle dynamic foreign table */
                switch ($modObjData['type']) {
                    case ModerationObject::TYPE_MESSAGE:
                        $modObjData['foreign_table'] = sprintf($modObjData['foreign_table'], sprintf('dialog_%d_%d', $uid, Yii::$app->request->post('did', 0)));

                        break;
                    case ModerationObject::TYPE_COMMENT:
                        $modObjData['foreign_table'] = sprintf($modObjData['foreign_table'], sprintf('com_%s_%d', $type, $oid));

                        break;
                }

                $modObjData['foreign_key'] = $oid;
                $modObjData['user_id'] = $uid;
                $modObjData['comment'] = $message;
                $modObjData['task_added'] = gmdate('Y-m-d H:i:s');
                $modObjData['task_finished'] = null;
                $modObjData['task_price'] = (new ModerationManager)->getSetting('Violation cost', 2);
                $modObjData['task_deadline'] = gmdate('Y-m-d H:i:s', time() + 3600 * 24);
                $modObjData['mod_category'] = ModerationObject::MODERATION_CATEGORY_VIOLATION;
                $modObjData['task_interval'] = 1440;
                $modObjData['priority'] = ModerationObject::getPriorityByType($modObjData['type']);

                try {
                    $mm->addModeration($modObjData);
                    /* send message to user @link http://redmine.suffra.com/projects/suffra/wiki/%D1%85%D1%83%D0%BA%D0%B8_%22%D0%A1%D0%BE%D0%BE%D0%B1%D1%89%D0%B5%D0%BD%D0%B8%D0%B9%22 */
                    (new SuffraEventTranslator())->translate(['name' => 'SEND_SYS_MESSAGE', 'data' => [
                            'from_id' => -2,
                            'to_id' => $uid,
                            'text_message' => T::t('Your violation has been put to processing queue')
                    ]]);
                    $response = ['success' => true, 'error' => false];
                } catch (Exception $exc) {
                    $response = ['success' => false, 'error' => [
                            'id' => $exc->getCode(),
                            'message' => $exc->getMessage()
                        ]
                    ];
                }

                return $response;

                break;
            case SuffraEvents::FILE_ATTRIBUTE_CHANGE:
                /* skip all atributes changes that is not in market */
                if (!isset($eventData['attributes']))
                    return;

                if ($eventData['attributes']['in_market'] == 1) {

                    $factory = function($key, $eventData) use ($mm) {

                        $modObjData['type'] = ModerationObject::getInternalType($eventData['type']);
                        $modObjData['foreign_table'] = ModerationObject::$typesToTables[$modObjData['type']];
                        $modObjData['foreign_key'] = $eventData['foreign_key'];
                        $modObjData['user_id'] = $eventData['user_id'];
                        $modObjData['comment'] = T::t('This file was put to market');
                        $modObjData['task_added'] = gmdate('Y-m-d H:i:s');
                        $modObjData['task_finished'] = null;
                        $modObjData['task_price'] = 2;
                        $modObjData['task_deadline'] = gmdate('Y-m-d H:i:s', time() + 3600 * 24);
                        $modObjData['mod_category'] = ModerationObject::MODERATION_CATEGORY_FILES;
                        $modObjData['task_interval'] = 1440;
                        $modObjData['priority'] = ModerationObject::getPriorityByType($modObjData['type']);

                        $mm->addModeration($modObjData);
                        $mm->updateModeratedObjectStatus($eventData['foreign_table'], $eventData['foreign_key'], ModerationObject::STATUS_ON_MODERATION);
                    };

                    if (is_array($eventData['foreign_key'])) {
                        foreach ($eventData['foreign_key'] as $key)
                            $factory($key, $eventData);
                    } else {
                        $factory($eventData['foreign_key'], $eventData);
                    }
                }

                break;

            case SuffraEvents::FILE_PERMISSION_CHANGE:
                /* skip all other atributes but all */
                if (!isset($eventData['access']) || 'all' != $eventData['access'])
                    return;

                $factory = function($key, $eventData) use ($mm) {
                    $modObjData['type'] = ModerationObject::getInternalType($eventData['type']);
                    $modObjData['foreign_table'] = ModerationObject::$typesToTables[$modObjData['type']];
                    $modObjData['foreign_key'] = $key;
                    $modObjData['user_id'] = $eventData['user_id'];
                    $modObjData['comment'] = T::t('Access of this file was changed to public');

                    $modObjData['task_added'] = gmdate('Y-m-d H:i:s');
                    $modObjData['task_finished'] = null;
                    $modObjData['task_price'] = 2;
                    $modObjData['task_deadline'] = gmdate('Y-m-d H:i:s', time() + 3600 * 24);
                    $modObjData['mod_category'] = ModerationObject::MODERATION_CATEGORY_FILES;
                    $modObjData['priority'] = ModerationObject::getPriorityByType($modObjData['type']);
                    $modObjData['task_interval'] = 1440;

                    $mm->addModeration($modObjData);
                    $mm->updateModeratedObjectStatus($eventData['foreign_table'], $eventData['foreign_key'], ModerationObject::STATUS_ON_MODERATION);
                };

                if (is_array($eventData['foreign_key'])) {
                    foreach ($eventData['foreign_key'] as $key)
                        $factory($key, $eventData);
                } else {
                    $factory($eventData['foreign_key'], $eventData);
                }


                break;

            case SuffraEvents::GOOD_ADDED:

                $modObjData['type'] = ModerationObject::getInternalType($eventData['type']);
                $modObjData['foreign_table'] = ModerationObject::$typesToTables[$modObjData['type']];
                $modObjData['foreign_key'] = $eventData['foreign_key'];
                $modObjData['user_id'] = $eventData['user_id'];
                $modObjData['comment'] = T::t('New good was added');

                $modObjData['task_added'] = gmdate('Y-m-d H:i:s');
                $modObjData['task_finished'] = null;
                $modObjData['task_price'] = 2;
                $modObjData['task_deadline'] = gmdate('Y-m-d H:i:s', time() + 3600 * 24);
                $modObjData['mod_category'] = ModerationObject::MODERATION_CATEGORY_GOODS;
                $modObjData['priority'] = ModerationObject::getPriorityByType($modObjData['type']);
                $modObjData['task_interval'] = 1440;

                $mm->addModeration($modObjData);
                $mm->updateModeratedObjectStatus($eventData['foreign_table'], $eventData['foreign_key'], ModerationObject::STATUS_ON_MODERATION);
                break;
        }

        Yii::info(sprintf("Processed suffra site event [%s] with data: %s", $eventName, json_encode($eventData)));

        return ['success' => true, 'error' => 'false'];
    }

    public function actionApply_moderation_action() {
        $mm = new ModerationManager();
        $action = Yii::$app->request->post('ac');
        $oid = Yii::$app->request->post('oid');

        if (!$mm->addModeratorAction($action, $oid)) {
            $result = ['success' => false, 'error' => ['id' => 1, 'message' => 'Error ocurred while processing moderator action']];
        } else {
            $result = ['success' => true, 'error' => false];
        }

        return $result;
    }

    /* public function actionChange_moderator_rating() {
      $response = $this->prepareResponse();
      $user = User::find(intval($_REQUEST['uid']));
      if ($user && !in_array($user->getRole(), ['supermoderator', 'admin']))
      return $response->setContent(json_encode(['success' => false, 'error' => ['id' => 1, 'message' => 'Not allowed']]));

      $moderatorId = intval($_REQUEST['mod_id']);
      $rating = intval($_REQUEST['rating']);
      if ($this->getModerationManager()->changeRating($moderatorId, $rating)) {
      /* reset cache
      User::find($moderatorId, false);
      $response->setContent(json_encode(['success' => true, 'error' => false]));
      }

      return $response;
      }


      $response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));

      return $response;
      }

      public function actionSearch() {
      $response = $this->prepareResponse();

      $moderationManager = $this->getModerationManager();
      $criteria = array();
      if (isset($_REQUEST['q']))
      $criteria['limit'] = intval($_REQUEST['l']);

      if (isset($_REQUEST['p']))
      $criteria['priority'] = intval($_REQUEST['p']);

      if (isset($_REQUEST['c']))
      $criteria['query'] = $_REQUEST['c'];

      /* existing objects foir work
      if (isset($_REQUEST['m']) && $_REQUEST['m']) {
      $criteria['existing'] = (bool) $_REQUEST['m'];
      $criteria['moderator'] = $_REQUEST['mid'];
      } else {
      $criteria['taskLocker'] = User::find($_REQUEST['mid']);
      }

      $objects = $moderationManager->findModerationObjectsByCriteria($criteria);

      $result = ['success' => $moderationManager->serializeForJson($objects), 'error' => false];

      $response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));

      return $response;
      }

      public function actionGet_moderator_counters() {
      $response = $this->prepareResponse();
      $result = ['success' => $this->getModerationManager()->getExpiredNonExpiredCounters(), 'error' => false];

      $response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));

      return $response;
      }

      public function moderatorActions() {
      $mManager = $this->getModerationManager();

      $result = [];

      /* @var $action ModeratorAction
      foreach ($mManager->getStatisticsByCriteria([
      'user' => $this->user
      ]) as $action) {
      $modObject = $action->getModerationObject();
      $result[] = [
      'date' => $action->getCreated()->getTimestamp(),
      'action' => $action->get(),
      'modObject' => [
      'type' => $modObject->getHumanReadableObjectType($modObject->getType()),
      'id' => $modObject->getId()
      ],
      'price' => $modObject->getTaskPrice()
      ];
      }


      $response = $this->prepareResponse();
      $response->setContent(json_encode(['success' => $result, 'error' => false], JSON_UNESCAPED_UNICODE));

      return $response;
      }

      public function actionGet_moderator_amount() {
      $mManager = $this->getModerationManager();
      $objects = $mManager->getStatisticsByCriteria(['user' => $this->user, 'getAmount' => true]);

      $amount = 0;
      foreach ($objects as $o) {
      $amount += $o->getField('amount');
      }

      $response = $this->prepareResponse();
      $response->setContent(json_encode(['success' => $amount, 'error' => false], JSON_UNESCAPED_UNICODE));

      return $response;
      }

      protected function prepareResponse() {

      $response = new Response();
      $response->setHeader('Cpontent-Type', 'application/json;charset=utf8');

      return $response;
      }
     */
}
