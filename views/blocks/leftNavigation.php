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
        foreach (\app\models\Stock::query()->orderBy(['name' => SORT_ASC])->all() as $item) {
            $url = Url::to(['cabinet/stock_item3', 'id' => $item['id']]);
            ?>
        <a href="<?= $url ?>" class="list-group-item<?= ($url == Url::current())? ' active' : '' ?>">
            <?= $item['name'] ?>
        </a>
        <?php } ?>
    </div>


<?php endif; ?>
