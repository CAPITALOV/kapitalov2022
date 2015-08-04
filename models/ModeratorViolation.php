<?php

namespace app\models;

use app\models\User;
use app\models\UserSuffra;
use yii\db\ActiveRecord;

class ModeratorViolation extends ActiveRecord {

    const TABLE = 'adm_moderation_violations';

    /**
     *
     * @var User
     */
    protected $moderator;
    
    /**
     *
     * @var UserSuffra
     */
    protected $user;
    public $id;
    public $action;
    public $user_id;
    public $moderator_id;
    public $msg;
    public $created;



    /**
     * 
     * @return Moderator
     */
    public function getModerator() {
        if (isset($this->moderator))
            return $this->moderator;


        $this->moderator = $this->hasOne('app\models\User', ['id' => 'moderator_id'])->one();

        return $this->moderator;
    }

    /**
     * 
     * @return User
     */
    public function getUser() {
        if (isset($this->user))
            return $this->user;

        $this->user = UserSuffra::find($this->user_id);
        
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return self::TABLE;
    }

}
