<?php

namespace app\models;

use app\service\RegistrationDispatcher;
use cs\base\DbRecord;
use cs\services\Security;
use yii\db\Query;
use yii\debug\models\search\Db;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;
use app\models\UserStock;

class User extends DbRecord implements \yii\web\IdentityInterface
{
    use UserCache;

    const TABLE = 'cap_users';

    /**
     * Возвращает аватар
     * Если не установлен то возвращает заглушку
     *
     * @return string
     */
    public function getAvatar($isFullPath = false)
    {
        $avatar =  $this->getField('avatar', '');
        if ($avatar == '') {
            $avatar = '/images/iam.png';
        }

        return Url::to($avatar, $isFullPath);
    }

    /**
     * Возвращает реферальную ссылку
     *
     * @param bool $isScheme
     *
     * @return string
     */
    public function getReferalLink($isScheme = false)
    {
        return Url::to(['auth/registration_referal', 'code' => $this->getField('referal_code')], $isScheme);
    }

    /**
     * Возвращает имя полное (Имя Фамилия)
     * Если не установлено то возвращает email
     *
     * @return string
     */
    public function getNameFull()
    {
        $arr[] = $this->getField('name_first');
        $arr[] = $this->getField('name_last');
        $name = join(' ', $arr);
        if (trim($name) == '') {
            return $this->getEmail();
        }

        return $name;
    }

    /**
     * Имеет ли пользователь роль
     *
     * @param int $id идентификатор роли
     *
     * @return bool
     */
    public function isRole($id)
    {
        return UserRoleLink::isRole($this->getId(), $id);
    }

    public function activate()
    {
        $this->update([
            'is_active'         => 1,
            'is_confirm'        => 1,
            'datetime_activate' => gmdate('YmdHis'),
        ]);
        UserStock::add($this->getId(), 1, 0);
    }

    /**
     * Validates password
     *
     * @param  string $password password to validate
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->getField('password'));
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Регистрирует пользователей
     *
     * @param $email
     * @param $password
     *
     * @return static
     */
    public static function registration($email, $password)
    {
        $email = strtolower($email);
        $fields = [
            'email'                    => $email,
            'password'                 => self::hashPassword($password),
            'is_active'                => 0,
            'is_confirm'               => 0,
            'datetime_reg'             => gmdate('YmdHis'),
            'referal_code'             => Security::generateRandomString(20),
        ];
        \Yii::info('REQUEST: ' . \yii\helpers\VarDumper::dumpAsString($_REQUEST), 'gs\\user_registration');
        \Yii::info('Поля для регистрации: ' . \yii\helpers\VarDumper::dumpAsString($fields), 'gs\\user_registration');
        $user = self::insert($fields);
        $fields = RegistrationDispatcher::add($user->getId());
        \cs\Application::mail($email, 'Подтверждение регистрации', 'registration', [
            'url'      => Url::to([
                'auth/registration_activate',
                'code' => $fields['code']
            ], true),
            'user'     => $user,
            'datetime' => \Yii::$app->formatter->asDatetime($fields['date_finish'])
        ]);

        return $user;
    }

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return User
     */
    public static function findIdentity($id) {
        return self::find($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // не используется пока
        return null;
    }

    /**
     * Finds user by username
     *
     * @param  string      $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::find(['email' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getField('id');
    }

    public function getEmail()
    {
        return $this->getField('email');
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return md5($this->getEmail() . '12345');
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Оплачена акция?
     *
     * @param integer $stock_id идентификатор акции cap_stock.id
     *
     * @return bool
     * true -  оплачена
     * false - не оплачена
     */
    public function isPaid($stock_id)
    {
        return UserStock::isPaidStatic($this->getId(), $stock_id);
    }

    /**
     * Пользователь админ?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->getField('is_admin', 0) == 1;
    }

    /**
     * @param string $password некодированный пароль
     *
     * @return bool
     */
    public function setPassword($password)
    {
        return $this->update([
            'password' => self::hashPassword($password)
        ]);
    }
}
