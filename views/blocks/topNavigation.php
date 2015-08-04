<?php

use app\models\Translator as T;
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
                <li><a class="navbar-brand" href="https://www.suffra.com/" target="_blank">Suffra.com</a></li>
                <li><span class="navbar-brand">/</span></li>
                <li><a class="navbar-brand" href="/"><?= T::t('Administration panel') ?></a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="/login"><span class="glyphicon glyphicon-user" aria-hidden="true">&nbsp;</span><?= T::t('Login') ?></a>
                    <?php else: ?>
                        <a href="/profile"><span class="glyphicon glyphicon-user" aria-hidden="true">&nbsp;</span><?= Yii::$app->user->identity->username; ?></a>
                    <?php endif; ?>
                </li>
                <?php if (isset($this->context->user->identity)): ?>
                    <li><a href="/logout"><span class="glyphicon glyphicon-log-out" aria-hidden="true">&nbsp;</span><?= T::t('Logout') ?></a></li>
                    <?php endif ?> 
            </ul>
        </div>
    </div>
</nav>
