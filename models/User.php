<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;

class User extends \yii\base\Object implements \yii\web\IdentityInterface {

    const TABLE = 'adm_user';

    const ROLE_SUPER_ADMIN      = 1;
    const ROLE_ADMIN            = 2;
    const ROLE_SUPER_MODERATOR  = 3;
    const ROLE_MODERATOR        = 4;
    const ROLE_EDITOR           = 5;
    const ROLE_SUPER_BUH        = 6;
    const ROLE_BUH              = 7;

    public $id;
    public $username;
    public $name_last;
    public $name_first;
    public $full_name;
    public $password;
    public $authKey;
    public $accessToken;
    public $roles;
    public $rating;
    private static $allRoles;

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return User
     */
    public static function findIdentity($id) {
        $query = new Query();
        $row = $query->select('adm_user.*')->from(self::TABLE)->where(['adm_user.id' => $id])->one();
        if (!$row) {
            return null;
        } else {
            $roles = $query
                    ->select('*')
                    ->from('adm_user_role')
                    ->innerJoin('adm_user_role_link', 'adm_user_role_link.role_id = adm_user_role.id')
                    ->where(['adm_user_role_link.user_id' => $id])
                    ->all();
            return new static([
                'id'          => $row['id'],
                'username'    => $row['email'],
                'name_last'   => $row['name_last'],
                'name_first'  => $row['name_first'],
                'full_name'   => implode(' ', [$row['name_first'], $row['name_last']]),
                'password'    => $row['password'],
                'authKey'     => $row['id'] . 'testKey',
                'accessToken' => $row['id'] . '-token',
                'roles'       => $roles,
                'rating'      => $row['rating'],
            ]);
        }
    }

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return User
     */
    public static function find($id) {
        return self::findIdentity($id);
    }

    /**
     * проверяет что пользователь имеет роль $roleId
     * @return boolean
     * true - пользователь имеет роль $roleId среди своего множества ролей
     * false - пользователь не имеет роль $roleId среди своего множества ролей
     */
    public function hasRole($roleId) {
        foreach ($this->roles as $role) {
            if ($role['id'] == $roleId) return true;
        }

        return false;
    }

    /**
     * Возвращет список идентификаторов ролей
     * @return array [1,2,3, ...]
     */
    public function getRoleIdArray() {
        $arr = [];
        foreach ($this->roles as $role) {
            $arr[] = $role['id'];
        }

        return $arr;
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
        $query = new Query();
        $row = $query->select('*')->from(self::TABLE)->where(['email' => $username])->one();
        if (!$row) {
            return null;
        } else {
            $id = $row['id'];
            $roles = $query
                ->select('*')
                ->from('adm_user_role')
                ->innerJoin('adm_user_role_link','adm_user_role_link.role_id = adm_user_role.id')
                ->where(['adm_user_role_link.user_id' => $id])
                ->all();

            return new static([
                'id'          => $row['id'],
                'username'    => $row['email'],
                'name_last'   => $row['name_last'],
                'name_first'  => $row['name_first'],
                'full_name'   => implode(' ', [$row['name_first'], $row['name_last']]),
                'password'    => $row['password'],
                'authKey'     => $row['id'] . 'testKey',
                'accessToken' => $row['id'] . '-token',
                'roles'       => $roles,
                'rating'      => $row['rating'],
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
//        return password_verify($password, $this->password);
        return (md5($password) == $this->password);
    }

    /**
     * Удаляет пользователя
     * @return boolean
     */
    public function delete() {
        $query = new Query();
        $command = $query->createCommand();
        $command->delete(self::TABLE, ['id' => $this->getId()])->execute();
        $command->delete('adm_user_role_link', ['user_id' => $this->getId()])->execute();

        return true;
    }

    /**
     * Выдает все роли
     * [
     * id => integer
     * name => string
     * code => string
     * ]
     * @return array
     */
    public static function getRoles() {
        if (is_null(self::$allRoles)) {
            $query = new Query();
            self::$allRoles = $query->select('*')->from('adm_user_role')->orderBy('id')->all();
        }

        return self::$allRoles;
    }
    
    public function getRoleIds() {
        static $roles;
        
        if(isset($roles))
            return $roles;
        
        $q = new Query();
        $r = $q->select('ar.role_id')
                ->from(self::TABLE . ' u')
                ->leftJoin('adm_user_role_link ar', 'ar.user_id = u.id')
                ->where('u.id=' . $this->id)->all();

        $roles = array_map(function ($a) {
                return (int)$a['role_id'];
            }, $r);
        
        return $roles;
    }

    /**
     * Выдает все роли
     * [
     * id => name,
     * ...
     * ]
     * @return array
     */
    public static function getRolesIndex() {
        $roles = self::getRoles();
        return ArrayHelper::map($roles, 'id', 'name');
    }
    
}
