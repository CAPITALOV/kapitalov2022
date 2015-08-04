<?php

namespace app\models;

use app\models\Translator as T;
use yii\db\ActiveRecord;
use yii\db\Query;

class Grossbuch extends ActiveRecord {

    const TABLE = 'itrix.grossbuch';
    const SYSTEM_USER = -1;
    /* accounts */
    const ACCOUNT_SYSTEM_BALLS = 2;
    const ACCOUNT_USER_BALLS = 1;
    const ACCOUNT_OUT = 3;
    const ACCOUNT_GOODS = 4;
    const ACCOUNT_FILES = 5;
    const ACCOUNT_FILES_DOWNLOADS = 7;
    const ACCOUNT_PRESENTS = 6;
    const ACCOUNT_MODERATOR_BALLS = 8;
    /* subacounts */
    const PAYMENT_BUY_GOODS = 1;
    const PAYMENT_WIN_GOODS = 2;
    const PAYMENT_UP_GOODS = 3;
    const PAYMENT_SELL_FILES = 4;
    const PAYMENT_BUY_FILES = 5;
    const PAYMENT_DOWNLOAD_BONUS_FOR_FILES = 6;
    const PAYMENT_SELL_BALLS = 7;
    const PAYMENT_BUY_BALLS = 8;
    const PAYMENT_SELL_GIFTS = 9;
    const PAYMENT_BUY_GIFTS = 10;
    const PAYMENT_TRANSFER_GIFTS = 11;

    public $id;
    public $debet;
    public $credit;
    public $src;
    public $csc;
    public $dst;
    public $dsc;
    public $val;
    public $dtstamp;
    public $desc;

    public function rules() {
        return [[
        'id',
        'debet',
        'credit',
        'src',
        'csc',
        'dst',
        'dsc',
        'val',
        'dtstamp',
        'desc',
            ], 'number'];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return self::TABLE;
    }

    public static function getAccountName($code) {
        static $accounts;

        if (!isset($accounts)) {
            $accounts = [
                static::ACCOUNT_SYSTEM_BALLS => T::t('System account'),
                static::ACCOUNT_USER_BALLS => T::t('User account'),
                static::ACCOUNT_OUT => T::t('Out account'),
                static::ACCOUNT_GOODS => T::t('Goods account'),
                static::ACCOUNT_FILES => T::t('Files account'),
                static::ACCOUNT_FILES_DOWNLOADS => T::t('Files downloads account'),
                static::ACCOUNT_PRESENTS => T::t('Presents account'),
            ];
        }

        return isset($accounts[$code]) ? $accounts[$code] : T::t('Unknown account');
    }

    /**
     * 
     * @param int $uid
     * @param int $date_start
     * @param int $date_end
     * @return array
     */
    public function GetHistory($uid, $date_start, $date_end) {
        return \Yii::$app->itrix->createCommand()->setSql(sprintf('CALL GetHistory(%d,%d,%d)', $uid, $date_start, $date_end))->queryAll();
    }

    public function GetCash($user_id) {
        return (int) \Yii::$app->itrix->createCommand()->setSql(sprintf('SELECT GetCash(%d);', $user_id))->queryScalar();
    }

    public function MoveCash($from, $to, $balls) {
        return (int) \Yii::$app->itrix->createCommand()->setSql(sprintf('SELECT MoveCash(%d,%d,%d);', $from, $to, $balls))->queryScalar();
    }

    /**
     * Добавляет деньги в кошелек
     * @return integer
     */
    public function AddCash($user_id, $amount) {
        return (int) \Yii::$app->itrix->createCommand()->setSql(sprintf('SELECT AddCash(%d,%d);', $user_id, $amount))->queryScalar();
    }

    /**
     *  `debet` INT,`credit` INT,`src` INT,`dst` INT,`debet_subcount` INT,`credit_subcount` INT,`balls` INT,`descr` VARCHAR(2048) 
     *
     * @param int    $debet            Код дебетового счета @see defines.inc.php
     * @param int    $credit           Код кредитного счета @see defines.inc.php
     * @param int    $src              ID пользователя совершающего платеж
     * @param int    $dst              ID пользователя кому отправляется платеж
     * @param int    $balls            Сумма баллов
     * @param int    $debetSubcount    Дебетовый субсчет для сложных операций
     * @param int    $creditSubcount   Кредитовый субсчет 
     * @param string $desc             Описание платежа (ссылка и т п)
     * 
     *
     * @return int
     */
    public function addGrossbuch($debet, $credit, $src, $dst, $balls, $desc = '', $debetSubcount = null, $creditSubcount = null) {
        if (is_array($desc))
            $desc = json_encode($desc, JSON_UNESCAPED_UNICODE);
        return (int) \Yii::$app->itrix->createCommand()->setSql(sprintf('SELECT AddGrossbuch(%d,%d,%d,%d,%s,%s,%d,\'%s\')', $debet, $credit, $src, $dst, $debetSubcount == null ? : 'NULL', $creditSubcount == null ? : 'NULL', $balls, $desc))->execute();
    }

    /**
     * @todo reimplement on Yii
     * Создает заявку на ввод или вывод денег
     * @param int $userId
     * @param array $data
     * @param int $type self::ORDER_TYPE_*
     * @return int insert id
     */
    public function createOrder($userId, $data, $type = self::ORDER_TYPE_IN_PAYMENT) {
        if (is_array($data))
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);

        $sql = SqlBuilder::replaceValues('SELECT AddOrder(:uid,:data,:type)', [
                    'uid' => $userId,
                    'data' => $data,
                    'type' => $type
        ]);

        return (int) $this->inDB->query_vote($sql)->fetch_array()[0];
    }

    /**
     * @todo reimplement on Yii
     * Set processed flag for order
     * @param int $orderId
     * @param bool $isProcessed
     * @return int $orderId
     */
    public function setProcessedOrder($orderId, $isProcessed = false) {
        $sql = SqlBuilder::replaceValues('CALL SetProcessedOrder(:oid,:ispoc)', [
                    'oid' => $orderId,
                    'isproc' => $isProcessed,
        ]);

        $this->inDB->query_vote($sql);

        return (int) $orderId;
    }

    /**
     * @todo reimplement on Yii
     * 
     * @param int $userId
     * @param int $isPaid
     * @return array
     */
    public function getOrders($userId, $isPaid = 0) {
        $sql = sprintf('CALL GetOrders(%d,%d)', $userId, $isPaid);
        $res = $this->GetArray($sql);
        /* @link http://habrahabr.ru/post/21326/ */
        while ($this->inDB->db_link_vote->next_result())
            $this->inDB->db_link_vote->store_result();

        return $res;
    }

    /**
     * @todo reimplement on Yii
     * @param int $orderId
     * @return array
     */
    public function getOrder($orderId) {
        $sql = sprintf('CALL GetOrder(%d)', $orderId);

        $res = $this->inDB->query_vote($sql)->fetch_assoc();
        /* @link http://habrahabr.ru/post/21326/ */
        while ($this->inDB->db_link_vote->next_result())
            $this->inDB->db_link_vote->store_result();

        return $res;
    }

    /**
     * @todo reimplement on Yii
     * @param int $orderId
     * @return mixed
     */
    public function deleteOrder($orderId) {
        $sql = sprintf('CALL DeleteOrder(%d)', $orderId);
        $res = $this->GetArray($sql);

        /* @link http://habrahabr.ru/post/21326/ */
        while ($this->inDB->db_link_vote->next_result())
            $this->inDB->db_link_vote->store_result();

        return $res;
    }

    /**
     * @todo reimplement on Yii
     * @param int $orderId
     * @param string $data
     * @return mixed
     */
    public function updateOrder($orderId, $data) {
        if (is_array($data))
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $sql = sprintf('CALL UpdateOrder(%d,\'%s\')', $orderId, $data);

        $res = $this->inDB->query_vote($sql);

        /* @link http://habrahabr.ru/post/21326/ */
        while ($this->inDB->db_link_vote->next_result())
            $this->inDB->db_link_vote->store_result();

        return $res;
    }

}
