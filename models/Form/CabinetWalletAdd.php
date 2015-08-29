<?php

namespace app\models\Form;

use app\models\NewsItem;
use app\models\Stock;
use app\models\StockKurs;
use app\models\StockPrognosis;
use app\models\User;
use app\models\WalletHistory;
use app\services\GsssHtml;
use cs\services\Str;
use cs\services\VarDumper;
use Yii;
use yii\base\Model;
use cs\Widget\FileUpload2\FileUpload;
use yii\db\Query;
use yii\helpers\Html;

/**
 * ContactForm is the model behind the contact form.
 */
class CabinetWalletAdd extends \cs\base\BaseForm
{
    public $monthCounter;

    function __construct($fields = [])
    {
        static::$fields = [
            [
                'monthCounter',
                'Количество месяцев',
                1,
                'integer',
            ],
        ];
        parent::__construct($fields);
    }

    public function add($stock_id)
    {
        if ($this->validate()) {
            $dateFinish = \app\models\UserStock::add(\Yii::$app->user->getId(), $stock_id, $this->monthCounter);
            $stock = Stock::find($stock_id);
            $dateFinish = Yii::$app->formatter->asDate($dateFinish);
            WalletHistory::insert("Оплачена акция: {$stock->getName()}, месяцев: {$this->monthCounter}, оплачено до: {$dateFinish}");

            return true;
        } else {
            return false;
        }
    }
}
