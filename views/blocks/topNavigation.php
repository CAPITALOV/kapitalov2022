<?php

use app\models\Translator as T;
use yii\helpers\Url;
use yii\helpers\Html;

?>

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only"><?= T::t('Hide navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-left">
                <li><a class="navbar-brand" href="http://capitalov.com/" target="_blank">Capitalov.com</a></li>
                <li><span class="navbar-brand">/</span></li>
                <li><a class="navbar-brand" href="/"><?= T::t('Administration panel') ?></a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right" style="margin-right: 20px;">
                <?php if (\Yii::$app->user->isGuest): ?>
                    <li>
                        <!-- Split button -->
                        <div class="btn-group" style="margin-top: 9px; opacity: 0.5;" id="loginBarButton">
                            <button type="button" class="btn btn-default" id="modalLogin"><i class="glyphicon glyphicon-user" style="padding-right: 5px;"></i>Войти</button>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="<?= Url::to(['auth/password_recover']) ?>">Напомнить пароль</a></li>
                                <li><a href="<?= Url::to(['auth/registration']) ?>">Регистрация</a></li>
                            </ul>
                        </div>
                    </li>
                <?php else:


                    ?>
                    <li class="dropdown">
                        <a
                            href="#"
                            class="dropdown-toggle"
                            data-toggle="dropdown"
                            aria-expanded="false"
                            role="button"
                            style="padding: 5px 10px 5px 10px;"
                            >
                            <?= Html::img(Yii::$app->user->identity->getAvatar(), [
                                'height' => '40px',
                                'class' => 'img-circle'
                            ]) ?>
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?= Url::to(['cabinet/objects']) ?>">Мои объединения</a></li>
                            <li><a href="<?= Url::to(['cabinet/poseleniya']) ?>">Мои поселения</a></li>
                            <li><a href="<?= Url::to(['site/profile']) ?>"><i class="glyphicon glyphicon-cog" style="padding-right: 5px;"></i>Мой профиль</a></li>
                            <li><a href="<?= Url::to(['cabinet/password_change']) ?>"><i class="glyphicon glyphicon-asterisk" style="padding-right: 5px;"></i>Сменить пароль</a></li>
                            <li class="divider"></li>
                            <li><a href="<?= Url::to(['auth/logout']) ?>" data-method="post"><i class="glyphicon glyphicon-off" style="padding-right: 5px;"></i>Выйти</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
