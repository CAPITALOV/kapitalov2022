<?php

namespace app\models;

use app\models\ModerationObject;
use app\models\Translator as T;
use app\models\User;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @author Andrew Lykov <andrew.lykov@yandex.ru>
 */
class ModeratorAction extends ActiveRecord {

    const TABLE = 'adm_moderation_actions';
    const ACTION_DISAPPROVE = 0;
    const ACTION_APPROVE = 1;
    const ACTION_PUT_TO_QUEUE = 2;

    public $id;
    public $action;
    public $object_id;
    public $moderator_id;
    public $created;
    public $task_price;

    /**
     *
     * @var User
     */
    protected $moderator;

    /**
     * @var ModerationObject
     */
    protected $moderationObject;

    public function rules() {
        return [
            [['action', 'object_id', 'moderator_id', 'task_price'], 'integer'],
            [['created'], 'string']
        ];
    }

    /**
     * 
     * @return ModerationObject|null
     */
    public function getModerationObject() {
        if (isset($this->moderationObject))
            return $this->moderationObject;

        $this->moderationObject = $this->hasOne('app\models\ModerationObject', ['id' => 'object_id'])->one();

        return $this->moderationObject;
    }

    /**
     * 
     * @return User
     */
    public function getModerator() {
        if (isset($this->moderator))
            return $this->moderator;

        $this->moderator = User::findIdentity($this->moderator_id);

        return $this->moderator;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return self::TABLE;
    }

    /**
     * setup search function for filtering and sorting 
     * based on fullName field
     */
    public function search($params, Query $query) {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /**
         * Setup your sorting attributes
         * Note: This is setup before the $this->load($params) 
         * statement below
         */
        $dataProvider->setSort([
            'attributes' => [
                'action' => [
                    'label' => T::t('Action'),
                    'asc' => ['action' => SORT_ASC],
                    'desc' => ['action' => SORT_DESC],
                    'default' => SORT_ASC,
                ],
                'created' => [
                    'label' => T::t('Date'),
                    'asc' => ['created' => SORT_ASC, 'action' => SORT_ASC],
                    'desc' => ['created' => SORT_DESC, 'action' => SORT_ASC],
                    'default' => SORT_DESC,
                ],
                'task_price' => [
                    'label' => T::t('Reward'),
                    'asc' => ['task_price' => SORT_ASC, 'action' => SORT_ASC],
                    'desc' => ['task_price' => SORT_DESC, 'action' => SORT_ASC],
                    'default' => SORT_DESC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if (isset($params[$this->formName()])) {
            foreach ($params[$this->formName()] as $prop => $propValue) {
                if ("" != $propValue && property_exists($this, $prop)) {
                    /* map objet_id to type */
                    if (in_array($prop, ['object_id', 'task_price']))
                        $query->andWhere(sprintf('%s=%d', $prop, $this->$prop));
                    else
                        $query->andWhere(sprintf('%s LIKE "%%%s%%"', $prop, $this->$prop));
                }
            }
        }

        return $dataProvider;
    }

}
