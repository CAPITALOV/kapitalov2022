<?php

use app\models\User;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Translator as T;

?>
<?php if (!\Yii::$app->user->isGuest): ?>
    <ul class="nav nav-pills nav-stacked">

        <?php if (\Yii::$app->user->identity->isAdmin()) { ?>
            <h4>Админ</h4>
            <ul class="list-unstyled col-md-offset-1">
                <li><a href="<?= Url::to(['superadmin_stock/index']) ?>">Курсы</a></li>
            </ul>
            <hr>
        <?php } ?>
        <h4><a href="<?= Url::to(['cabinet/stock_list']) ?>">Курсы</a></h4>
    </ul>
<?php endif; ?>
