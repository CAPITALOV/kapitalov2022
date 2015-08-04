<?php

namespace app\models;

use app\events\ModerationEvents;
use app\models\Form\ModeratorSetting;
use app\models\ModerationObject;
use app\models\ModeratorAction;
use app\models\ModeratorViolation;
use app\models\Translator as T;
use app\models\User;
use app\models\UserSuffra;
use app\service\SuffraEventTranslator;
use cmsLogger;
use DateTime;
use Exception;
use mysqli_result;
use Suffra\Module\Moderation\Exception\ModerationException;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use app\models\Grossbuch as G;

/**
 * @author Andrew Lykov <andrew.lykov@yandex.ru>
 */
class ModerationManager {

    const TABLE_SETTINGS = 'adm_moderation_settings';
    const TABLE_VIOLATIONS = 'adm_moderation_violations';

    /**
     *
     * @var array
     */
    protected $lockedTaskIds;

    /**
     *
     * @var bool
     */
    public $isReturnQueryInstance = false;

    /**
     * 
     * @param bool $mode
     * @return ModerationManager
     */
    public function setReturnQueryInstance($mode) {
        $this->isReturnQueryInstance = $mode;

        return $this;
    }

    /**
     * 
     * @param $criteria [limit,priority]
     * @throws ModerationException
     * @return Query|array
     */
    public function findModerationObjectsByCriteria($criteria) {
        $q = new Query();

        $q->select('mo.*,us.nickname,mu.mod_user_id as user_id')
                ->from(ModerationObject::TABLE . ' mo')
                ->leftJoin(ModerationObject::TABLE_RELATION . ' mu', 'mo.id = mu.mod_obj_id')
                ->leftJoin(UserSuffra::TABLE . ' us', 'mu.mod_user_id = us.id')
                ->where('mo.moderation_status IS NULL')
                ->orWhere('mo.moderation_status = 0');

        /* construct dynamic query */
        if (isset($criteria['priority'])) {
            $q->andWhere('mo.priority >= ' . $criteria['priority']);
        }

        if (isset($criteria['query']) && $criteria['query'] == 'expired')
            $q->andWhere('UTC_TIMESTAMP() > mo.task_deadline');

        if (isset($criteria['query']) && $criteria['query'] == '1h')
            $q->andWhere('UTC_TIMESTAMP() >= DATE_SUB(mo.task_deadline,INTERVAL 1 HOUR)')
                    ->andWhere('UTC_TIMESTAMP() < mo.task_deadline');

        if (isset($criteria['limit']))
            $q->limit($criteria['limit']);

        if (isset($criteria['existing']) && $criteria['existing'])
            $q->andWhere('mu.moderator_id = ' . $criteria['moderator']);


        /* lock tasks */
        if (isset($criteria['moderator']) && isset($criteria['lock'])) {
            foreach ($q->all() as $row) {
                $this->lockTaskByModerator($criteria['moderator'], $row['id']);
            }
        }

        if ($this->isReturnQueryInstance)
            return $q;

        return $q->all();
    }

    public function getModeratorRating($moderatorId) {
        static $rating;

        if (!$targetModerator = User::findIdentity($moderatorId))
            return null;

        if (isset($rating[$targetModerator->id]))
            return $rating[$targetModerator->id];


        $q = new Query();

        $rawRating = $q->select([new Expression('MAX(rating) as max, MIN(rating) as min')])
                        ->from(User::TABLE . ' u')
                        ->leftJoin('adm_user_role_link ar', 'ar.user_id = u.id')
                        ->where('ar.role_id IN(' . implode(',', $targetModerator->getRoleIds()) . ')')->one();


        $rawRating['current'] = $targetModerator->rating;

        if ($rawRating['current'] == 0) {
            $rawRating['is_positive'] = null;
            $rawRating['percentage'] = 0;
            $rawRating['max'] = 0;
            $rawRating['min'] = 0;
        } else {
            $rawRating['is_positive'] = $rawRating['current'] > 0 ? 1 : 0;
            if (!$rawRating['is_positive']) {
                $rawRating['max'] = abs($rawRating['min']) + $rawRating['max'];
                $rawRating['min'] = 0;
                $rawRating['current'] = abs($rawRating['current']);
            }
            $rawRating['percentage'] = round(($rawRating['is_positive'] ? $rawRating['current'] / $rawRating['max'] : $rawRating['current'] / (abs($rawRating['min']) + $rawRating['max'])) * 100, 2);
        }

        $rating[$targetModerator->id] = $rawRating;

        return $rawRating;
    }

    public function getModeratorHistory(User $moderator) {
        $q = new Query();
        $q->select(ModeratorAction::TABLE . ' a')
                ->leftJoin(ModerationObject::TABLE . ' o', 'o.id = a.object_id')
                ->leftJoin(ModerationObject::TABLE_RELATION . ' or', 'or.mod_obj_id = a.object_id');
    }

    /**
     * @todo error checking
     * @param User $moderator
     * @param array $postData
     */
    public function updateModeratorProfile(User $moderator, $postData) {
        (new Query())->createCommand()->update(UserSuffra::TABLE, $postData, 'id = ' . $moderator->id);

        return true;
    }

    /**
     * @param int $moderatorId
     * @param int $taskId
     */
    public function lockTaskByModerator($moderatorId, $taskId) {
        $q = new Query();
        if (!isset($this->lockedTaskIds)) {
            $this->lockedTaskIds = array();
            $q->select('mod_obj_id as id')
                    ->from(ModerationObject::TABLE_RELATION)
                    ->where('moderator_id = :id', [':id' => $moderatorId]);

            $this->lockedTaskIds = array_map(function($a) {
                return $a['id'];
            }, $q->all());
        }

        if (!in_array($taskId, $this->lockedTaskIds))
            $q->createCommand()->update(ModerationObject::TABLE_RELATION, ['moderator_id' => $moderatorId], 'mod_obj_id=' . $taskId)->execute();
    }

    /**
     * 
     * @param array $criteria
     * @param int $limit
     * @param int $offset
     * @return Query|array
     */
    public function getStatisticsByCriteria($criteria, $limit = 10, $offset = 0) {
        $q = new Query();
        $q->select('ac.*,mo.type,mo.id as pid,mo.foreign_key,mo.task_price')
                ->from(ModeratorAction::TABLE . ' ac')
                ->where('ac.moderator_id = :id', [':id' => $criteria['user_id']])
                ->leftJoin(ModerationObject::TABLE . ' mo', 'ac.object_id = mo.id')
                ->orderBy('ac.created');

        if (isset($criteria['getAmount'])) {
            $q->select('ac.*,mo.task_price as amount')
                    ->leftJoin(ModerationObject::TABLE . ' mo', 'mo.id = ac.object_id')
                    ->groupBy('ac.action');
        }

        if (isset($criteria['to']) && $criteria['to'] instanceof DateTime)
            $q->andWhere('ac.created <= :f', [':f' => $criteria['to']->format('Y-m-d H:i:s')]);

        if (isset($criteria['from']) && $criteria['from'] instanceof DateTime)
            $q->andWhere('ac.created >= :t', [':t' => $criteria['from']->format('Y-m-d H:i:s')]);

        if (isset($criteria['limit']))
            $q->limit($criteria['limit']);
        if (isset($criteria['offset']))
            $q->offset($criteria['offset']);

        if ($this->isReturnQueryInstance)
            return $q;


        return $q->all();
    }

    public function getModeratorAmount($moderatorId) {
        return (new Query())->select([new Expression('SUM("mo.task_price") as amount')])
                        ->from(ModerationObject::TABLE . ' mo')
                        ->leftJoin(ModeratorAction::TABLE . ' ma', 'ma.object_id = mo.id')
                        ->where('ma.moderator_id=' . $moderatorId)
                        ->andWhere('ma.action!=' . ModeratorAction::ACTION_PUT_TO_QUEUE)->scalar();
    }

    /**
     * 
     * @param int $moderatorId
     * @param int $userId
     * @param str $msg
     * @return boolean
     * @throws ModerationException
     */
    public function addModeratorViolation($moderatorId, $userId, $msg) {
        (new Query())->createCommand()->insert(ModeratorViolation::TABLE, [
            'user_id' => $userId,
            'moderator_id' => $moderatorId,
            'msg' => $msg,
            'created' => new Expression('UTC_TIMESTAMP()')
        ])->execute();


        return true;
    }

    public function getModeratorViolations($moderatorId) {
        $q = new Query();
        $q->select(['v.*', 'u.nickname as user_name', new Expression('CONCAT_WS(" ",au.name_first,au.name_last) as moderator_name')])
                ->from(ModeratorViolation::TABLE . ' v')
                ->leftJoin(UserSuffra::TABLE . ' u', 'u.id=v.user_id')
                ->leftJoin(User::TABLE . ' au', 'au.id=v.moderator_id')
                ->where('moderator_id = :m', [':m' => $moderatorId]);

        if ($this->isReturnQueryInstance)
            return $q;

        return $q->all();
    }

    public function changeRating($moderatorId, $value) {
        if (!$moderator = User::findIdentity($moderatorId))
            return false;
        (new Query())->createCommand()->update(User::TABLE, ['rating' => $moderator->rating + $value], 'id = ' . $moderatorId)->execute();

        return true;
    }

    public function getModerators() {
        $q = new Query();

        $q->select(sprintf('u.*,(SELECT COUNT(*) FROM %s WHERE moderator_id = u.id) as violations,(SELECT COUNT(*) FROM %s WHERE moderator_id = u.id) AS actions', self::TABLE_VIOLATIONS, ModeratorAction::TABLE))
                ->from(User::TABLE . ' u')
                ->leftJoin('adm_user_role_link ur', 'u.id = ur.user_id', ['ur.role_id' => User::ROLE_MODERATOR])
                ->where('ur.role_id=' . User::ROLE_MODERATOR);

        if ($this->isReturnQueryInstance)
            return $q;


        return $q->all();
    }

    public function getCounterOf($type, $params = null) {
        $q = new Query();
        switch ($type) {
            case 'moderators':
                return (int) $q->from(User::TABLE . ' u')
                                ->leftJoin('adm_user_role_link ur', 'u.id = ur.user_id', ['ur.role_id' => User::ROLE_MODERATOR])
                                ->count();
                break;
            case 'violations':
                return (int) $q->from(ModeratorViolation::TABLE . ' v')
                                ->where('v.moderator_id = ' . $params)
                                ->count();
                break;
        }
    }

    /**
     * Will return array with first key for with all objects
     * and second element for 1 hour expired objects
     * and third element for expired objects
     * 
     * @return array
     */
    public function getExpiredNonExpiredCounters() {
        $sql = implode(' UNION ', array(
                    (new Query())->select('COUNT(*) as v')
                    ->from(ModerationObject::TABLE)
                    ->where('(moderation_status IS NULL OR moderation_status = 0)')
                    ->createCommand()->getSql(),
                    (new Query())->select('COUNT(*) as v')
                    ->from(ModerationObject::TABLE)
                    ->where(new Expression('UTC_TIMESTAMP() >= DATE_SUB(task_deadline,INTERVAL 1 HOUR)'))
                    ->andWhere(new Expression('UTC_TIMESTAMP() < task_deadline'))
                    ->andWhere('(moderation_status IS NULL OR moderation_status = 0)')
                    ->createCommand()->getSql(),
                    (new Query())->select('COUNT(*) as v')
                    ->from(ModerationObject::TABLE)
                    ->where(new Expression('UTC_TIMESTAMP() > task_deadline'))
                    ->andWhere('(moderation_status IS NULL OR moderation_status = 0)')
                    ->createCommand()->getSql(),
        ));
        /* @var $result mysqli_result */
        $result = Yii::$app->db->createCommand($sql)->queryAll();

        return array_map(function($a) {
            return (int) $a['v'];
        }, $result);
    }

    /**
     * 
     * @param int $id
     * @return array|null
     */
    public function findModerationObjectById($id) {
        return (new Query())->select('o.*,u.nickname,u.id as user_id')
                        ->from(ModerationObject::TABLE . ' o')
                        ->leftJoin(ModerationObject::TABLE_RELATION . ' mu', 'mu.mod_obj_id = o.id')
                        ->leftJoin(UserSuffra::TABLE . ' u', 'mu.mod_user_id = u.id')
                        ->where('o.id=' . $id)->one();
    }

    /**
     * Checks existence of ModerationObject by type and foreign key
     * 
     * @param int $moderationType
     * @param int $foreignKey
     * @return bool
     */
    public function isModerationObjectExists($moderationType, $foreignKey) {
        return ModerationObject::find()->exists();
    }

    /**
     * 
     * @param int $type One of ModeratorAction::ACTION_*
     * @param int $objId
     * @return boolean
     * @throws ModerationException
     */
    public function addModeratorAction($type = ModeratorAction::ACTION_DISAPPROVE, $objId) {
        /* @var $modObject ModerationObject */
        /* @todo Check why fuckin ActiveRecord failing here */
        if (!$modObject = new ModerationObject((new Query())->select('*')->from(ModerationObject::TABLE)->where('id=' . $objId)->one()))
            return false;

        $statusMap = array(
            ModeratorAction::ACTION_APPROVE => ModerationObject::STATUS_MODERATED,
            ModeratorAction::ACTION_DISAPPROVE => ModerationObject::STATUS_BLOCKED,
            ModeratorAction::ACTION_PUT_TO_QUEUE => ModerationObject::STATUS_ON_MODERATION,
        );

        switch ($type) {
            case ModeratorAction::ACTION_APPROVE:
            case ModeratorAction::ACTION_DISAPPROVE:
            case ModeratorAction::ACTION_PUT_TO_QUEUE:
                if (!$this->updateModerationObjectStatus($objId, $statusMap[$type]))
                    Yii::warning(sprintf('Failed to update moderation object %s for action %d', $modObject->id, $type));
                if (!$this->unlockTaskByModerator($objId, $modObject->getModerator()->id))
                    Yii::warning(sprintf('Failed to uplock moderation object %s for action %d', $modObject->id, $type));
                if (!$this->updateModeratedObjectStatus($modObject->foreign_table, $modObject->foreign_key, $statusMap[$type]))
                    Yii::warning(sprintf('Failed to update moderated object %s:%d for action %d', $modObject->foreign_table,$modObject->foreign_key, $type));

                /* fire event */
                if ($type != ModeratorAction::ACTION_PUT_TO_QUEUE) {
                    
                    /* add grossbuh for moderator */
                    (new G())->addGrossbuch(G::ACCOUNT_SYSTEM_BALLS,G::ACCOUNT_MODERATOR_BALLS,G::SYSTEM_USER, $modObject->getModerator()->id, $modObject->task_price, [
                        'name' => str_replace(['{obj}', '{balls}'], [$modObject->id, $modObject->task_price], T::t('Earned {balls} for moderated object {obj}'))
                    ]);
                    
                    $eventName = $type == ModeratorAction::ACTION_APPROVE ? ModerationEvents::MODERATION_APPROVED : ($type == ModeratorAction::ACTION_DISAPPROVE ? ModerationEvents::MODERATION_DISSAPROVED : '');
                    $eventData = [
                        'foreign_key' => $modObject->foreign_key,
                        'foreign_table' => $modObject->foreign_table,
                        'moderator_id' => $modObject->getModerator()->id,
                        'type' => (new ModerationObject())->getHumanReadableObjectType($modObject->type)
                    ];

                    (new SuffraEventTranslator())->translate(['name' => $eventName, 'data' => $eventData]);

                    if ($type == ModeratorAction::ACTION_DISAPPROVE) {
                        /* send message to user @link http://redmine.suffra.com/projects/suffra/wiki/%D1%85%D1%83%D0%BA%D0%B8_%22%D0%A1%D0%BE%D0%BE%D0%B1%D1%89%D0%B5%D0%BD%D0%B8%D0%B9%22 */
                        (new SuffraEventTranslator())->translate(['name' => 'SEND_SYS_MESSAGE', 'data' => [
                                'from_id' => -2,
                                'to_id' => (new Query())->select('ou.mod_user_id')->from(ModerationObject::TABLE . ' o')
                                        ->leftJoin(ModerationObject::TABLE_RELATION . ' ou', 'ou.mod_obj_id = o.id')->where(['o.id' => $modObject->id])->scalar(),
                                'text_message' => T::t('Your moderation request was declined')
                        ]]);
                    } else {
                        if ($modObject->type == ModerationObject::TYPE_PROFILE) {
                            (new SuffraEventTranslator())->translate(['name' => 'SEND_SYS_MESSAGE', 'data' => [
                                    'from_id' => -2,
                                    'to_id' => $modObject->foreign_key,
                                    'text_message' => T::t('We requested violation for your profile, if we receive more violations about your profile - then it\'ll be blocked')
                            ]]);
                        } else {
                            (new SuffraEventTranslator())->translate(['name' => 'SEND_SYS_MESSAGE', 'data' => [
                                    'from_id' => -2,
                                    'to_id' => (new Query())->select('ou.mod_user_id')->from(ModerationObject::TABLE . ' o')
                                            ->leftJoin(ModerationObject::TABLE_RELATION . ' ou', 'ou.mod_obj_id = o.id')->where(['o.id' => $modObject->id])->scalar(),
                                    'text_message' => T::t('Your moderation request was fullfilled')
                            ]]);
                        }
                    }
                }
                break;
        }

        /* set task finished */
        if ($type != ModeratorAction::ACTION_PUT_TO_QUEUE)
            (new Query())->createCommand()->update(ModerationObject::TABLE, ['task_finished' => new Expression('UTC_TIMESTAMP()')], 'id = ' . $objId)->execute();

        (new Query())->createCommand()->insert(ModeratorAction::TABLE, [
            'action' => $type,
            'object_id' => $objId,
            'created' => new Expression('UTC_TIMESTAMP()'),
            'moderator_id' => $modObject->getModerator()->id
        ])->execute();

        return true;
    }

    /**
     * Updates linked object status
     * @param string $foreignTable
     * @param int $foreignKey
     * @param int $status
     * @return boolean
     */
    public function updateModeratedObjectStatus($foreignTable, $foreignKey, $status = ModerationObject::STATUS_ON_MODERATION) {
        try {
            (new Query())->createCommand()->update($foreignTable, ['moderation_status' => $status], 'id = ' . $foreignKey)->execute();
        } catch (Exception $exc) {
            return false;
        }

        return true;
    }

    /**
     * Updates main object status
     *
     * 
     * @param int $objId
     * @param int $status
     * @return boolean
     */
    public function updateModerationObjectStatus($objId, $status = ModerationObject::STATUS_ON_MODERATION) {
        try {
            (new Query())->createCommand()->update(ModerationObject::TABLE, ['moderation_status' => $status], 'id = ' . $objId)->execute();
        } catch (Exception $exc) {
            return false;
        }

        return true;
    }

    public function getSetting($key, $default = null, $moderatorId = null) {
        $q = new Query();
        $q->select('value')
                ->from(ModeratorSetting::TABLE)
                ->where(['key' => $key]);

        if (null != $moderatorId)
            $q->andWhere(['moderator_id' => $moderatorId]);

        return $q->exists() ? $q->scalar() : $default;
    }

    /**
     * 
     * @param int $objId
     * @param int $userId
     */
    public function unlockTaskByModerator($objId, $userId) {
        try {
            (new Query())->createCommand()->update(ModerationObject::TABLE_RELATION, [
                'moderator_id' => null,
                    ], 'mod_obj_id = :m AND moderator_id = :mid', [':m' => $objId, ':mid' => $userId])->execute();
        } catch (Exception $exc) {
            return false;
        }


        return true;
    }

    /**
     * @todo implement on Yii
     * Initializes database structure needed by Moderation component
     * @return boolean
     * @throws ModerationException
     */
    public function initDatabaseStructure() {
        $dbLink = parent::db()->db_link;

        /* return false if initialized */
        if ($dbLink->query(sprintf('SHOW CREATE TABLE adm_moderation_object;', ModerationObject::TABLE)))
            return false;

        $statements = array(sprintf(<<< stmt1
        CREATE TABLE IF NOT EXISTS %s (
            id INT NOT NULL AUTO_INCREMENT,
            `type` INT NOT NULL,
            foreign_table VARCHAR(50) DEFAULT NULL,
            foreign_key INT NOT NULL,
            mod_category INT NOT NULL,
            comment TEXT DEFAULT NULL,
            task_interval INT NOT NULL,
            task_added DATETIME NOT NULL,
            task_finished DATETIME DEFAULT NULL,
            task_deadline DATETIME DEFAULT NULL,
            priority INT NOT NULL DEFAULT 0,
            task_price VARCHAR(15) NOT NULL DEFAULT 0,
            moderation_status TINYINT(1) NULL DEFAULT NULL,
            PRIMARY KEY (id))
               ENGINE = MyISAM DEFAULT CHARSET=utf8
stmt1
                    , ModerationObject::TABLE), sprintf(<<<stmt2_1
        CREATE INDEX fk
            ON %s (foreign_key)
stmt2_1
                    , ModerationObject::TABLE), sprintf(<<<stmt2_2
        CREATE INDEX ft
            ON %s (foreign_table)
stmt2_2
                    , ModerationObject::TABLE), sprintf(<<<stmt3
        CREATE INDEX ta
            ON %s (task_added)
stmt3
                    , ModerationObject::TABLE), sprintf(<<<stmt3_01
        CREATE INDEX tf
            ON %s (task_finished)
stmt3_01
                    , ModerationObject::TABLE), sprintf(<<<stmt3_03
        CREATE INDEX td
            ON %s (task_deadline)
stmt3_03
                    , ModerationObject::TABLE), sprintf(<<<stmt3_02
        CREATE INDEX prt
            ON %s (priority)
stmt3_02
                    , ModerationObject::TABLE), sprintf(<<< stmt3_1
            CREATE TABLE %s (
                mod_obj_id INT NOT NULL,
                mod_user_id INT DEFAULT NULL,
                moderator_id INT DEFAULT NULL
            )
               ENGINE = MyISAM DEFAULT CHARSET=utf8
stmt3_1
                    , ModerationObject::TABLE_RELATION), sprintf(<<< stmt3_11
            CREATE INDEX uid 
                ON %s (mod_user_id)
stmt3_11
                    , ModerationObject::TABLE_RELATION), sprintf(<<< stmt3_12
            CREATE INDEX oid 
                ON %s (mod_obj_id)
stmt3_12
                    , ModerationObject::TABLE_RELATION), sprintf(<<< stmt3_13
            CREATE INDEX mid 
                ON %s (moderator_id)
stmt3_13
                    , ModerationObject::TABLE_RELATION), sprintf(<<<stmt4
        CREATE TABLE IF NOT EXISTS %s (
            id INT NOT NULL AUTO_INCREMENT,
            action INT NOT NULL,
            object_id INT NOT NULL,
            moderator_id INT NOT NULL,
            created DATETIME NOT NULL,
            PRIMARY KEY (id))
               ENGINE = MyISAM DEFAULT CHARSET=utf8
stmt4
                    , ModeratorAction::TABLE), sprintf(<<<stmt5
        CREATE INDEX oid
            ON adm_moderation_actions (object_id)
stmt5
                    , ModeratorAction::TABLE), sprintf(<<<stmt7
        CREATE INDEX ct
            ON adm_moderation_actions (created)
stmt7
                    , ModeratorAction::TABLE), sprintf(<<<stmt4
        CREATE TABLE IF NOT EXISTS %s (
            id INT NOT NULL AUTO_INCREMENT,
            `key` VARCHAR(100) NOT NULL,
            `value` TEXT NOT NULL,
            moderator_id INT NOT NULL,
            PRIMARY KEY (id))
               ENGINE = MyISAM DEFAULT CHARSET=utf8
stmt4
                    , ModerationManager::TABLE_SETTINGS), sprintf(<<<stmt4
        CREATE TABLE IF NOT EXISTS %s (
            id INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `moderator_id` INT NOT NULL,
            `msg` MEDIUMTEXT NOT NULL,
            created DATETIME NOT NULL,
            PRIMARY KEY (id))
               ENGINE = MyISAM DEFAULT CHARSET=utf8
stmt4
                    , ModerationManager::TABLE_VIOLATIONS));


        foreach ($statements as $stmt) {
            if (!$dbLink->query($stmt))
                throw new ModerationException(sprintf('%s:%s', $dbLink->error_list[0]['sqlstate'], $dbLink->error_list[0]['error']));
        }

        $this->manageModerationFields();

        return true;
    }

    /**
     * @todo implement on Yii
     * @return bool|mixed If errors return array with errors
     */
    public function clearDatabaseStructure() {
        $structureTables = array(
            ModerationObject::TABLE,
            ModeratorAction::TABLE,
            ModerationManager::TABLE_SETTINGS,
            ModerationManager::TABLE_VIOLATIONS
        );

        $conn = parent::db()->db_link;

        $errors = array();
        foreach ($structureTables as $tbl) {
            $sql = sprintf('DROP TABLE IF EXISTS %s', $tbl);
            if (!$result = $conn->query($sql)) {
                $errors[] = sprintf('Error query [%s] %s:%s', $sql, $conn->error_list[0]['sqlstate'], $conn->error_list[0]['error']);
            }
        }


        try {
            $this->manageModerationFields(true);
        } catch (Exception $exc) {
            $errors[] = $exc->getMessage();
        }


        return empty($errors) ? true : $errors;
    }

    /**
     * 
     * @param ModerationObject $object
     * @throws ModerationException
     * @return int Last insert id
     */
    public function addModeration($data = []) {
        if (!count($data))
            return;

        $userId = $data['user_id'];
        unset($data['user_id']);

        (new Query())->createCommand()->insert(ModerationObject::TABLE, $data)->execute();

        (new Query())->createCommand()->insert(ModerationObject::TABLE_RELATION, [
            'mod_user_id' => $userId,
            'mod_obj_id' => Yii::$app->db->getLastInsertID(),
            'moderator_id' => null
        ])->execute();

        return Yii::$app->getDb()->getLastInsertID();
    }

    /**
     * Adds is_moderated fields to all managed tables
     * @todo implement on Yii
     * @return bool
     * @throws ModerationException
     */
    public function manageModerationFields($remove = false) {
        $logger = cmsLogger::getInstance();
        $logger->setTriggerLevel(cmsLogger::LEVEL_DEBUG);
        $conn = parent::db()->db_link;

        $alterer = function($table) use ($logger, $conn, $remove) {
            /* @var $result mysqli_result */
            $result = $conn->query(sprintf('SELECT * FROM %s LIMIT 1', $table));
            $isModeratedExists = false;
            if ($result && $result->num_rows) {
                foreach ($result->fetch_fields() as $fields)
                /* @link http://php.net/manual/ru/mysqli-result.fetch-fields.php */
                    if ('moderation_status' === $fields->orgname && !$remove) {
                        $logger->log(sprintf('Column moderation_status already exists in %s', $table), cmsLogger::LEVEL_DEBUG);
                        return;
                    } elseif ('moderation_status' === $fields->orgname && $remove) {
                        $sql = sprintf('ALTER TABLE %s DROP COLUMN moderation_status', $table);
                        if (!$conn->query($sql))
                            throw new ModerationException(sprintf('%s:%s', $conn->error_list[0]['sqlstate'], $conn->error_list[0]['error']));
                        $logger->log(sprintf('Column moderation_status dropped from [%s] query [%s]', $table, $sql), cmsLogger::LEVEL_DEBUG);
                    }elseif ('moderation_status' === $fields->orgname) {
                        $isModeratedExists = true;
                    }

                if (!$isModeratedExists && !$remove) {
                    $sql = sprintf('ALTER TABLE %s ADD COLUMN moderation_status TINYINT(1) NULL DEFAULT NULL', $table);
                    $logger->log(sprintf('Adding column moderation_status to %s query [%s]', $table, $sql), cmsLogger::LEVEL_DEBUG);
                    if (!$conn->query($sql))
                        throw new ModerationException(sprintf('%s:%s', $conn->error_list[0]['sqlstate'], $conn->error_list[0]['error']));
                }
            }
        };

        foreach (ModerationObject::$typesToTables as $type => $table) {
            /* handler for dynamic tables */
            if (false !== strstr($table, '%s')) {
                $db = str_replace('.%s', '', $table);
                foreach ($conn->query(sprintf('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = "%s"', $db))->fetch_all() as $dynTable) {
                    $alterer($db . '.' . $dynTable[0]);
                }
                continue;
            }

            $alterer($table);
        }

        $logger->setTriggerLevel(cmsLogger::LEVEL_WARNING);
        return true;
    }

    /**
     * 
     * @param mixed $objects Array of ModerationObject instances
     */
    public function serializeForJson($objects) {
        $result = array();

        if (is_array($objects)) {
            /* @var $obj ModerationObject */
            foreach ($objects as $obj) {
                $result[] = array(
                    'id' => $obj->id,
                    'type' => $obj->getHumanReadableObjectType($obj->type),
                    'mod_object' => $obj->getModeratedObject()->toArray(),
                    'user' => array(
                        'id' => $obj->getUser()->getId(),
                        'nickname' => $obj->getUser()->nickname
                    ),
                    'priority' => $obj->priority,
                    'category' => $obj->mod_category,
                    'comment' => $obj->comment,
                    'taskEnd' => $obj->task_deadline,
                    'price' => $obj->price
                );
            }
        } else {
            $this->serializeForJson(array($objects));
        }

        return $result;
    }

}
