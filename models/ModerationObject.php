<?php

namespace app\models;

use app\models\ModeratedObject;
use app\models\User;
use app\models\UserSuffra;
use cms_model_filestorages;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * @author Andrew Lykov <andrew.lykov@yandex.ru>
 */
class ModerationObject extends ActiveRecord {

    const TABLE = 'adm_moderation_object';
    const TABLE_RELATION = 'adm_moderation_obj_users';

    /**
     * @var int
     */
    const MODERATION_CATEGORY_VIOLATION = 1;

    /**
     * @var int
     */
    const MODERATION_CATEGORY_GOODS = 2;

    /**
     * @var int
     */
    const MODERATION_CATEGORY_FILES = 3;

    /**
     * @var int
     */
    const MODERATION_CATEGORY_PHOTOS = 4;

    /**
     * @var int
     */
    const STATUS_ON_MODERATION = 0;

    /**
     * @var int
     */
    const STATUS_MODERATED = 1;

    /**
     * @var int
     */
    const STATUS_BLOCKED = 2;

    /**
     * Маппинг типов объектов
     * @var array
     */
    public static $types = array(
        'file' => self::TYPE_FILE,
        'photo' => self::TYPE_PHOTO,
        'video' => self::TYPE_VIDEO,
        'audio' => self::TYPE_AUDIO,
        'document' => self::TYPE_DOCUMENT,
        'prog' => self::TYPE_PROGRAMM,
        'comment' => self::TYPE_COMMENT,
        'good' => self::TYPE_GOOD,
        'profile' => self::TYPE_PROFILE,
        'message' => self::TYPE_MESSAGE,
        'news_item' => self::TYPE_NEWS_ITEM,
        'article' => self::TYPE_ARTICLE,
        'communities' => self::TYPE_COMMUNITY,
        'humor' => self::TYPE_HUMOR,
        'game' => self::TYPE_GAME,
        'tv_channel' => self::TYPE_TV_CHANNEL,
    );

    /**
     * Маппинг типов объектов к таблицам
     * @var array 
     */
    public static $typesToTables = array(
        self::TYPE_FILE => 'cms_user_files',
        self::TYPE_VIDEO => 'cms_user_video',
        self::TYPE_PHOTO => 'cms_user_photos',
        self::TYPE_FILE => 'cms_user_files',
        self::TYPE_DOCUMENT => 'cms_user_files',
        self::TYPE_PROGRAMM => 'cms_user_files',
        self::TYPE_GOOD => 'cms_goods',
        self::TYPE_AUDIO => 'cms_music',
        self::TYPE_COMMENT => 'site_comments.%s',
        self::TYPE_MESSAGE => 'site_dialogs.%s',
        self::TYPE_PROFILE => 'cms_users',
        self::TYPE_NEWS_ITEM => 'cms_news_items',
        self::TYPE_ARTICLE => 'cms_content',
        self::TYPE_COMMUNITY => 'cms_communities',
        self::TYPE_HUMOR => 'cms_entertainment_humor',
        self::TYPE_GAME => 'cms_entertainment_games',
        self::TYPE_TV_CHANNEL => 'cms_entertainment_tv',
    );

    /**
     *
     * @var array
     */
    public static $priorityByType = array(
        self::TYPE_FILE => 30,
        self::TYPE_VIDEO => 40,
        self::TYPE_PHOTO => 60,
        self::TYPE_FILE => 30,
        self::TYPE_DOCUMENT => 60,
        self::TYPE_PROGRAMM => 70,
        self::TYPE_GOOD => 90,
        self::TYPE_AUDIO => 20,
        self::TYPE_COMMENT => 60,
        self::TYPE_MESSAGE => 70,
        self::TYPE_PROFILE => 100,
        self::TYPE_NEWS_ITEM => 40,
        self::TYPE_ARTICLE => 50,
        self::TYPE_COMMUNITY => 50,
        self::TYPE_HUMOR => 10,
        self::TYPE_GAME => 5,
        self::TYPE_TV_CHANNEL => 40,
    );

    /**
     * @var int Объект аудио
     * @see cms_model_filestorages line 570
     */
    const TYPE_AUDIO = 1;

    /**
     * @var int Объект видео
     * @see cms_model_filestorages line 570
     */
    const TYPE_VIDEO = 2;

    /**
     * @var int Объект фото
     * @see cms_model_filestorages line 570
     */
    const TYPE_PHOTO = 3;

    /**
     * @var int Объект файла
     * @see cms_model_filestorages line 570
     */
    const TYPE_FILE = 100;

    /**
     * @var int Объект документа
     * @see cms_model_filestorages line 570
     */
    const TYPE_DOCUMENT = 4;

    /**
     * @var int Объект программы
     * @see cms_model_filestorages line 570
     */
    const TYPE_PROGRAMM = 5;

    /**
     * @var int Объект опроса
     * @see cms_model_filestorages line 570
     */
    const TYPE_GOOD = 6;

    /**
     * @var int Объект комментария
     * @see cms_model_filestorages line 570
     */
    const TYPE_COMMENT = 7;

    /**
     * @var int Объект профиля пользователя
     */
    const TYPE_PROFILE = 8;

    /**
     * @var int Объект сообщения диалога
     */
    const TYPE_MESSAGE = 9;

    /**
     * @var int Объект новости
     */
    const TYPE_NEWS_ITEM = 10;

    /**
     * @var int Объект статьи
     */
    const TYPE_ARTICLE = 11;

    /**
     * @var int
     */
    const TYPE_COMMUNITY = 12;

    /**
     * @var int Объект анекдота
     */
    const TYPE_HUMOR = 15;

    /**
     * @var int Объект игры
     */
    const TYPE_GAME = 16;

    /**
     * @var int Объект тв канала
     */
    const TYPE_TV_CHANNEL = 17;

    /**
     *
     * @var UserSuffra
     */
    protected $user;

    /**
     *
     * @var User
     */
    protected $moderator;

    /**
     *
     * @var ModeratedObject
     */
    protected $moderatedObject;
    public $id;
    public $type;
    public $foreign_table;
    public $foreign_key;
    public $mod_category;
    public $comment;
    public $task_interval;
    public $task_added;
    public $task_finished;
    public $task_deadline;
    public $priority;
    public $task_price;
    public $moderation_status;

    public function rules() {
        return [[
        'id',
        'type',
        'foreign_table',
        'foreign_key',
        'mod_category',
        'comment',
        'task_interval',
        'task_added',
        'task_finished',
        'task_deadline',
        'priority',
        'task_price',
        'moderation_status',
            ],
            'safe'];
    }

    /**
     * Get User
     * @return User|null
     */
    public function getUser() {
        if (isset($this->user))
            return $this->user;

        $this->user = $this->hasOne('app\models\UserSuffra', ['id' => 'user_id'])->one();

        return $this->user;
    }

    public function getModerator() {
        if (isset($this->moderator))
            return $this->moderator;

        $this->moderator = User::findIdentity((new Query())->select('mr.moderator_id')
                                ->from(ModerationObject::TABLE . ' o')
                                ->leftJoin(self::TABLE_RELATION . ' mr', 'o.id = mr.mod_obj_id')
                                ->where('o.id = ' . $this->id)->scalar());

        return $this->moderator;
    }

    /**
     * 
     * @return int
     */
    public static function getPriorityByType($type) {
        if (!isset(self::$priorityByType[$type]))
            return 0;
        elseif (isset(self::$priorityByType[$type]))
            return self::$priorityByType[$type];
    }

    /**
     * @return int
     */
    public static function getInternalType($stringType) {
        if (isset(self::$types[$stringType]))
            return self::$types[$stringType];

        return -1;
    }

    /**
     * 
     * @param int $internalType One of self::TYPE_* constants
     * @return string human readable type string
     */
    public function getHumanReadableObjectType($internalType) {
        foreach (self::$types as $hr => $int) {
            if ($internalType == $int)
                return $hr;
        }

        return 'unknown';
    }

    /**
     * @return ModeratedObject
     */
    public function getModeratedObject() {
        if (isset($this->moderatedObject))
            return $this->moderatedObject;

        ModeratedObject::$tableName = isset($this->foreign_table) ? $this->foreign_table : self::$typesToTables[$this->type];

        $this->moderatedObject = (new Query())->select('*')
                ->from(ModeratedObject::tableName())
                ->where(['id' => $this->foreign_key])->one();
        
        return $this->moderatedObject;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return self::TABLE;
    }

}
