<?php

namespace app\models\Form;

use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\User;

class ModeratorSetting extends ActiveRecord {

    const TABLE = 'adm_moderation_settings';

    /**
     *
     * @var User
     */
    protected $moderator;
    public $id;
    public $key;
    public $value;
    public $moderator_id = null;

    public function rules() {
        return [
            [['key', 'value'], 'required'],
        ];
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
     * @todo find out why Yii insert saves just id
     */
    public function insert($runValidation = true, $attributes = NULL) {
        
        (new Query())->createCommand()->insert(self::TABLE, [
            /* @todo add moderator link in future */
            'moderator_id' => 0,
            'key' => $this->key,
            'value' => $this->value
        ])->execute();
        
        return true;
    }
    /**
     * @todo find out why Yii insert saves just id
     */
    public function update($runValidation = true, $attributes = NULL) {
        if(null === $attributes)
            return false;
        (new Query())->createCommand()->update(self::TABLE, [
            /* @todo add moderator link in future */
            'moderator_id' => 0,
            'key' => $attributes['key'],
            'value' => $attributes['value']
        ], 'id=' . $this->id)->execute();
        
        return true;
    }

}
