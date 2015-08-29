<?php

namespace app\models;

use cs\base\BaseForm;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends BaseForm
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function __construct($config = [])
    {
        self::$fields = [
            [
                'username',
                'Email',
                1,
                'email',
                [],
            ],
            [
                'password',
                'Пароль',
                1,
                'validatePassword',
            ],
            [
                'rememberMe',
                'Запомнить меня',
                0,
                'cs\Widget\CheckBox2\Validator',
                'widget' => [
                    'cs\Widget\CheckBox2\CheckBox', []
                ]
            ],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (is_null($user)) {
                $this->addError($attribute, 'Пользователь не найден');
                return;
            }
            if ($user->getField('is_confirm') != 1) {
                $this->addError($attribute, 'Пользователь не активирован');
                return;
            }
            if ($user->getField('is_active') != 1) {
                $this->addError($attribute, 'Пользователь заблокирован');
                return;
            }
            if ($user->getField('password') == '') {
                $this->addError($attribute, 'Вы  не завели себе пароль для аккаунта. Зайдите в восстановление пароля');
                return;
            }
            if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Не правильное имя или пароль');
                return;
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
