<?php

use app\models\User;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Translator as T;

?>
<?php if (!\Yii::$app->user->isGuest): ?>
    <?php if (\Yii::$app->user->identity->isAdmin()) { ?>
        <div class="list-group">
            <a href="<?= Url::to(['superadmin_stock/index']) ?>" class="list-group-item">
                Админ
            </a>
        </div>
    <?php } ?>

    <div class="list-group">
        <a href="<?= Url::to(['cabinet/stock_list']) ?>" class="list-group-item">
            Курсы
        </a>
    </div>


    <div class="list-group">

        <?php
        foreach (\app\models\Stock::query()
                     ->leftJoin('cap_users_stock_buy', 'cap_users_stock_buy.stock_id = cap_stock.id and cap_users_stock_buy.date_finish > :date', [':date' => date('Y-m-d')])
                     ->select([
                         'cap_stock.id',
                         'cap_stock.name',
                         'if(ifnull(cap_users_stock_buy.id, 0) = 0,0,1) as is_paid',
                     ])
                     ->orderBy(['cap_stock.name' => SORT_ASC])
             ->all() as $item) {
            $url = Url::to(['cabinet/stock_item3', 'id' => $item['id']]);
            ?>
        <a href="<?= $url ?>" class="list-group-item<?= ($url == Url::current())? ' active' : '' ?>">
            <?= $item['name'] ?>
            <?php if ($item['is_paid'] == 1) {?>
                <span class="badge"><i class="glyphicon glyphicon-ok"></i></span>
            <?php } ?>
        </a>
        <?php } ?>
    </div>


<?php endif; ?>
