<?php

use app\models\User;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Translator as T;
?>
<?php if (!\Yii::$app->user->isGuest): ?>
    <ul class="nav nav-pills nav-stacked">
        <?php if (in_array(User::ROLE_SUPER_ADMIN, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/superAdmin">superAdmin</a></h4>
            <ul class="list-unstyled col-md-offset-1">
                <li><a href="/adminUsers">Админы</a></li>
				<li><a href="<?= Url::to(['superadmin_stock/index'])?>">Курсы</a></li>
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_ADMIN, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/admin">admin</a></h4>
            <ul class="list-unstyled col-md-offset-1">
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_SUPER_MODERATOR, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/superModerator"><?= T::t('Super moderator menu') ?></a></h4>
            <ul class="list-unstyled col-md-offset-1">
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_MODERATOR, \Yii::$app->user->identity->getRoleIds())): ?>
            <h4><?= T::t('Moderator menu') ?></h4>
            <ul class="list-unstyled col-md-offset-1">
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_EDITOR, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/editor">editor</a></h4>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_SUPER_BUH, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/superBuh">superBuh</a></h4>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_BUH, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/buh">buh</a></h4>
            <hr>
        <?php endif; ?>
    </ul>
<?php endif; ?>
