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
                <li><a href="/components">Компоненты</a></li>
                <li><a href="/modules">Модули</a></li>
                <li><a href="/plugins">Плагины</a></li>
                <li><a href="/topMenu">Верхнее меню</a></li>
                <li><a href="/userMenu">Меню пользователя</a></li>
                <li><a href="/jsLogger">JsLogger</a></li>
                <li><a href="/log">Лог пользователей</a></li>
                <li><a href="/cron">Задачи крон</a></li>
                <li><a href="/settings">Настройки сайта</a></li>
				<li><a href="/check_files/db">Проверка файлов</a></li>
				<li><a href="<?= Url::to(['superadmin/clear_cache'])?>">Очистить кеш</a></li>
				<li><a href="<?= Url::to(['superadmin_communities/index'])?>">Управление категориями сообществ</a></li>
				<li><a href="<?= Url::to(['superadmin_voting_price_list/index'])?>">Опросы, цена ответа и кол-во ответов</a></li>
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_ADMIN, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/admin">admin</a></h4>
            <ul class="list-unstyled col-md-offset-1">
                <li><a href="/users">Пользователи</a></li>
                <li><a href="/news">Новостные ленты</a></li>
				<li><a href="/gifts">Подарки</a></li>
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_SUPER_MODERATOR, \Yii::$app->user->identity->getRoleIds())): ?>

            <h4><a href="/superModerator"><?= T::t('Super moderator menu') ?></a></h4>
            <ul class="list-unstyled col-md-offset-1">
                <li><a href="/moderators"><?= T::t('Moderators') ?></a></li>
                <li><a href="<?= Url::to(['supermoderator/moderators_settings']) ?>"><?= T::t('Moderators settings') ?></a></li>
            </ul>
            <hr>
        <?php endif; ?>
        <?php if (in_array(User::ROLE_MODERATOR, \Yii::$app->user->identity->getRoleIds())): ?>
            <h4><?= T::t('Moderator menu') ?></h4>
            <ul class="list-unstyled col-md-offset-1">
                <?php if (1 == count(\Yii::$app->user->identity->roles)): ?>
                    <li class="<?= Url::to() == Url::to(['moderator/profile']) ? 'label-info' : '' ?>"><?= Html::a(Html::encode(T::t('Profile')), Url::to(['moderator/profile'])) ?></li>
                <?php endif ?>
                <li class="<?= Url::to() == Url::to(['moderator/work']) ? 'label-info' : '' ?>"><?= Html::a(Html::encode(T::t('Moderation')), Url::to(['moderator/work'])) ?></li>
                <li class="<?= Url::to() == Url::to(['moderator/history', 'id' => \Yii::$app->user->id]) ? 'label-info' : '' ?>"><?= Html::a(Html::encode(T::t('Your history')), Url::to(['moderator/history', 'id' => \Yii::$app->user->id])) ?></li>
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
