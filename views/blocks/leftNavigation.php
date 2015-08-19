<?php

use app\models\User;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Translator as T;
?>
<?php if (!\Yii::$app->user->isGuest): ?>
    <ul class="nav nav-pills nav-stacked">
        <h4><a href="/superAdmin">superAdmin</a></h4>
        <ul class="list-unstyled col-md-offset-1">
            <li><a href="/adminUsers">Админы</a></li>
            <li><a href="<?= Url::to(['superadmin_stock/index'])?>">Курсы</a></li>
        </ul>
        <hr>
    </ul>
<?php endif; ?>
